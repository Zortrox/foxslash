<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAABfCAMAAAAXiD8DAAAAS1BMVEWHQQzxcQoRCAH4dg7ubgibViH+cgEHAwD9cgAnEQALBQD3eR78cQBOIgD9cQCuTgDUXwBqMAD+cgCOQAD/cgC2UgDtawD/cgD/cwBUG3MvAAAAGHRSTlMACxIXJSc6OlthbnV7jJqbqrW3wtLl6+yur5JMAAAEUklEQVR42u2a65ajIBCEm0sABXFC8PL+T7rRNLbGdXTizJBzdmv3D8YMlQ/pAhV+V4wJeZcQDPJIKOPuMkrmscCkcT4E763J44Ap40KIwTurZA4DQjkfu64LIwL4dQ0jEGLf9zFYm8PAA0Df912wNsMQTAC66LNcA0KZBMBb9fsjMAMQrMkEICAAkwWAegDoY3A5ALA3AOBj9w4Aonc5AIh/HABkByCNIwASfl0IAOdgJgAxKwDzKgB21zliawBsw6UxbvHf+UHhLgX7Og9Adf2WDJwQAtiPoaLtt2RhX+dj6LJtwMEZMbEDYN+AZ3BCBCAigC8bCCcMYBEiALsG2tvtOug2qBlVHzBwPoY0GqhLPaooihKl4YSoCiOAPQMWZuLDPw4nxOTRHL7EZOBbdRQAXQMOvlPLHBZwngCToxg2vzGHddwnwJQLsbsrhhlOoe4SC5NKKcmoCh8BAPxjl4D0sZ8UXcp1N1oy1P/jgFrH0EkDJvYLBTX/YpTTeY9YU08xpMQxA44WAuIhPGBWcRkNfZEKtgxjszMAbAFAwCED0Y3yIYQ4Koj0u1YaMfO6xx5xAFLzaTso2CEDa3UjWzXxb5qm7VFBAvAU5FHOjX5cVjH0ooF4AQDmU/fXsiyra4NNxwD0taVBkAHPKziIOQAJLxvg9Lvaa6H5Xbq8IZ5hEDQ2ejMZbSv+JQA0lGtFAcDQ3U2ns5MDNzQKbESVjFZ8lcOvE5AAl+4xrppOLxq0N9rBIQkRQXG+iqHDBpqF2vbjkkagrYCkrzQGwCt0kECtY+iwgVu5UFUVHKBOl+N6FWsedqp23v92DO0bqPmzps8+OMykGypcOBUQoebPMSTZcQNu+zP3yUE+TYW24H8B8OMGaBa1l9VuSLIvGPDbn3n2lwWEhSkCUEEkACmGJBzQJX5ioE5Tbr2EwhgWfr6PeIohdtpAlfJlJofAUwSROsek2s7hnTWh/2TjGGYIZKSpSaU6JeEEICYAZwhQtXf4tyid6kX/1yYxeM7hkwR4qjMOGUiPXV7mWV3rAh3EJwAnDFDVGRWMlEJNq7OazyZAW1BF7AjAaQNPxT7G2FHRA5hH8Cya+6M5TAa2t8JU7ElU9AzRwJREUQx9ycDaMRX7JPrJVMEavbhcnqrweQNcp1FIaiqOZRLdpBnzEgAywGHTwa2ddX8r+KxMtjVfXS7HABDi23hbooZN8WE12rR3Nc210Dx1eLt/r9K0U6vbtqcYOiqOtyX0py51WVZ3laXmdHDU/IZAHA38zI15/hAsRe1FDGW9Md/H8BMAdkUAYpYb8zDelfynAcy3gxne0hgAhLcA0GUCICcAWZ4P434YAWR4NsXkOzwe7d+iCucCYN4FQO4qnAmAeBsAMQuAeQ5neUD+Pjk8VOEcV4D8n8MUQ3kAWJ8ZgM0aQ0xaGzJWYWByGIKuy7UQASaUcT7ETO9poQNrvbc5XlZFB1JZa7/+suofwn3LuIGlXbUAAAAASUVORK5CYII="/>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
