import { error } from '@sveltejs/kit';
import type { RequestHandler } from './$types';
import { STEAM_API_KEY } from '$env/static/private';
import { XMLParser } from 'fast-xml-parser';

export const prerender = false;

type ProfileData =
	| {
			response: {
				error: string;
			};
			profile?: never;
	  }
	| {
			response?: never;
			profile: {
				steamID64: string;
			};
	  };

export const GET: RequestHandler = async ({ url, params }) => {
	const steamID = params.steamid?.trim();
	if (!params.steamid?.trim()) {
		throw error(400, 'Steam ID not provided');
	}

	let steamID64 = steamID;
	if (url.searchParams.get('id64') !== '1') {
		const steamProfileURL = new URL(`http://steamcommunity.com/id/${steamID}/?xml=1`);
		const steamProfileData = await fetch(steamProfileURL).catch(() => null);
		if (!steamProfileData) {
			throw error(500, 'error fetching profile data');
		}

		const parser = new XMLParser();
		const profileData = parser.parse(await steamProfileData.text()) as ProfileData;

		const errorMessage = profileData.response?.error;
		if (errorMessage) {
			throw error(500, `error fetching profile data: ${errorMessage}`);
		}

		const newSteamID64 = profileData.profile?.steamID64;
		if (!newSteamID64) {
			throw error(500, `error fetching profile data: no steamID64 data`);
		}

		steamID64 = newSteamID64;
	}

	const steamFriendsListURL = new URL(
		'http://api.steampowered.com/ISteamUser/GetFriendList/v0001/'
	);
	steamFriendsListURL.searchParams.append('key', STEAM_API_KEY);
	steamFriendsListURL.searchParams.append('steamid', steamID64);
	steamFriendsListURL.searchParams.append('relationship', 'friend');

	const steamGamesURL = new URL('http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/');
	steamGamesURL.searchParams.append('key', STEAM_API_KEY);
	steamGamesURL.searchParams.append('include_appinfo', '1');
	steamGamesURL.searchParams.append('steamid', steamID64);

	const steamGamesData = await fetch(steamGamesURL)
		.then((data) => data.json())
		.catch(() => null);

	return new Response(JSON.stringify(steamGamesData));
};
