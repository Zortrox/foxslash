import { redirect } from '@sveltejs/kit';

export const load = ({ url }) => {
	const steamID = url.searchParams.get('steamid');
	const id64Param = url.searchParams.has('id64');
	if (steamID) {
		const newURL = new URL(url.origin + url.pathname);
		newURL.pathname += `/steamid/${steamID}`;

		if (id64Param) {
			newURL.searchParams.append('id64', '1');
		}

		throw redirect(301, newURL);
	}
};
