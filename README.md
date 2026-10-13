# Quantum Ethic — Air Quality Map

A live map of air quality across the United States from two independent sources — the Copernicus CAMS atmosphere model and the EPA AirNow monitor network — with day-by-day history since June 2025. Live at **https://quantumethic.org/air-quality/**.

## What it shows
- **Live now:** a 2° grid of modeled US AQI across the contiguous US, community markers with PM2.5, PM10, ozone and NO₂ (refreshed hourly), and EPA AirNow monitor sites as small dots (sensor readings, refreshed every 15 minutes). Where a community has a monitor within 60 km, the table shows model and sensor side by side.
- **History:** a date slider over a 4° grid from June 1, 2025 to the last build date; daily-mean PM2.5 for every community (line chart with crosshair); and a whole-period table — mean PM2.5, days over the EPA 24-hour threshold (35.5 µg/m³), days over the annual standard (9 µg/m³), and each community's worst day.
- **Add a town:** any place can be added by name; it is stored only in the viewer's browser.

## Files
| File | Purpose |
|---|---|
| `proposals.html` | "Where would trees matter most?" — the written proposals, ranked live from `history.js`, with city-by-city asks addressed to each city's planting program. |
| `index.html` | The map. Loads Leaflet from cdnjs, basemap tiles from OpenStreetMap, live data from Open-Meteo, and `history.js` if present. |
| `places.js` | Volunteer communities and reference cities (`[name, lat, lon]`), plus a crude land mask for the grid. |
| `build-history.html` | Builds the history in the browser: fetches hourly PM2.5 for every grid square and community, averages by day, and saves `history.js` (to the server via `save-history.php` with a token, or as a download). |
| `save-history.php` (`save-history.example.php` here) | Token-protected endpoint that writes `history.js`. It only ever writes JSON data (re-encoded server-side), never code. |
| `airnow.php` (`airnow.example.php` here) | Server-side proxy for the EPA AirNow API: fetches the latest hour of PM2.5 and ozone observations for the contiguous US, caches for 15 minutes, keeps the API key off the client. |

## Data and methods
- Model source: the [Open-Meteo Air Quality API](https://open-meteo.com/en/docs/air-quality-api), serving the Copernicus Atmosphere Monitoring Service (CAMS) global model. These are **model estimates**; resolution is tens of kilometers.
- Sensor source: the [EPA AirNow API](https://docs.airnowapi.org/) (`/aq/data/` bounding-box query, AQI values, verbose). These are **measured readings** from regulatory monitors, reported hourly.
- Live US AQI is the API's `us_aqi` (worst pollutant at that hour). Historical AQI is computed from each day's mean PM2.5 with the EPA's 2024 PM2.5 breakpoints, the way the official daily PM2.5 AQI is calculated.
- Daily values are the mean of the 24 hourly model values (UTC). A day "exceeds" a threshold when its mean exceeds it.
- Grids are coarse by design (2° live, 4° history) and are sampled at each square's center.

## Deploy
1. Copy `save-history.example.php` to `save-history.php` and set `$TOKEN` to a long random string; copy `airnow.example.php` to `airnow.php` and set `$KEY` to a free [AirNow API key](https://docs.airnowapi.org/account/request/). (The live site's copies with real secrets are not in this repo.)
2. Upload `index.html`, `proposals.html`, `places.js`, `build-history.html`, `save-history.php`, `airnow.php` to a folder on any PHP-capable host (e.g. `public_html/air-quality/`).
3. Open `build-history.html`, enter the token, click Build; `history.js` is written next to the map. Re-run any time to extend the history.

## Credits
Built by Quantum Ethic (quantumethic.org). Started in summer 2025; live since 2026. Map tiles © OpenStreetMap contributors. Leaflet © Vladimir Agafonkin (BSD-2). Model data © Copernicus / Open-Meteo (CC BY 4.0); monitor data from EPA AirNow (public domain, preliminary and subject to revision).

## License
MIT
