<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Weather Radar Loop</title>
<meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
<script src="https://api.mapbox.com/mapbox-gl-js/v3.1.2/mapbox-gl.js"></script>
<meta http-equiv="refresh" content="1800">
<link href="https://api.mapbox.com/mapbox-gl-js/v3.1.2/mapbox-gl.css" rel="stylesheet">
<style>
    /* User specified styles */
    @font-face {
        font-family: "font";
        /* Placeholder for local font, fallback to sans-serif */
        src: url("/assets/fonts/inter_reg.ttf") format("truetype"); 
    }
    .font {
        font-family: "font", "Inter", sans-serif;
    }
    body {
        background-color: red;
        color: white;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }
    .toptext {
        background: linear-gradient(to right, #323230, #13191C);
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 15%;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding-right: 10px;
        box-sizing: border-box;
        font-size: 1.2rem;
        z-index: 10;
    }
    .radar {
        background-color: gray;
        position: absolute;
        top: 15%;
        left: 0;
        width: 100%;
        height: 85%;
    }
    #map {
        width: 100%;
        height: 100%;
    }
    .int_hide {
        opacity: 0;
    }
</style>
</head>
<body>

<div class="toptext font">PAST 3 HOURS</div>
<div class="radar">
    <div id="map"></div>
</div>

<script>
    // --- CONFIGURATION ---
    // 1. PUT YOUR MAPBOX TOKEN HERE
    mapboxgl.accessToken = 'pk.eyJ1Ijoid2VhdGhlciIsImEiOiJjbHAxbHNjdncwaDhvMmptcno1ZTdqNDJ0In0.iywE3NefjboFg11a11ON0Q';

    // 2. PUT YOUR TWC (THE WEATHER COMPANY) API KEY HERE
    const TWC_API_KEY = 'e1f10a1e78da46f5b10a1e78da96f525';
    // ---------------------
    const defaultLocation = [-89.07, 42.83];
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/goldbblazez/ckgc8lzdz4lzh19qt7q9wbbr9', 
        // style: 'mapbox://styles/tyetenna/cm4uiu2xk000a01s8emz0bn89',
        zoom: 7.7, 
        projection: 'mercator' 
    });

    // --- GEOLOCATION LOGIC ---
    // Automatically locate user on load

    fetch("https://pro.ip-api.com/json/?key=AmUN9xAaQALVYu6")
    .then(response => response.json())
    .then(data => {
        map.flyTo({
                center: [data.lon, data.lat],
                zoom: 5,
                essential: true
            });
    })

    // --- RADAR LOOP LOGIC ---
    
    // TWC usually provides 5-min intervals. 
    // 3 hours = 180 minutes. 180 / 5 = 36 frames.
    const frameCount = 36; 
    const intervalMinutes = 5;
    
    // Animation timing
    const loopDuration = 6000; // 6 seconds total loop
    const pauseDuration = 1000; // 1 second pause at end
    const frameDuration = loopDuration / frameCount; 

    // Generate timestamps for the past 3 hours (rounded down to nearest 5 min)
    function getRadarTimestamps() {
        const timestamps = [];
        const now = new Date();
        const coeff = 1000 * 60 * 5; // 5 minutes in ms
        // Round down current time to nearest 5 minute interval
        let roundedEnd = new Date(Math.floor(now.getTime() / coeff) * coeff);

        for (let i = frameCount - 1; i >= 0; i--) {
            const pastTime = new Date(roundedEnd.getTime() - (i * intervalMinutes * 60 * 1000));
            // TWC API expects unix timestamp in seconds
            timestamps.push(Math.floor(pastTime.getTime() / 1000));
        }
        return timestamps;
    }

    map.on('load', () => {
        if (TWC_API_KEY === 'YOUR_TWC_API_KEY_HERE') {
            console.error("Please insert your TWC API Key in the code.");
            alert("API Key Missing: Check console code");
            return;
        }

        const timestamps = getRadarTimestamps();

        // 1. Add all sources and layers first (hidden by default)
        timestamps.forEach((ts, index) => {
            const sourceId = `radar-source-${index}`;
            const layerId = `radar-layer-${index}`;

            // TWC Tile URL Structure
            // Note: 'radar' layer is standard. x:y:z is handled by mapbox.
            const tileUrl = `https://api.weather.com/v3/TileServer/tile/radar?ts=${ts}&xyz={x}:{y}:{z}&apiKey=${TWC_API_KEY}`;

            map.addSource(sourceId, {
                type: 'raster',
                tiles: [tileUrl],
                tileSize: 256
            });

            map.addLayer({
                id: layerId,
                type: 'raster',
                source: sourceId,
                paint: {
                    'raster-opacity': 0, // Start hidden
                    'raster-opacity-transition': { duration: 0 } // Instant switching
                }
            });
        });

        // 2. Animation Loop
        let frameIndex = 0;

        function animate() {
            // Hide previous frame (handle wrap-around)
            const prevFrameIndex = frameIndex === 0 ? frameCount - 1 : frameIndex - 1;
            map.setPaintProperty(`radar-layer-${prevFrameIndex}`, 'raster-opacity', 0);

            // Show current frame
            map.setPaintProperty(`radar-layer-${frameIndex}`, 'raster-opacity', 0.8);

            // Calculate delay for next frame
            let nextDelay = frameDuration;
            
            // If this is the last frame, pause longer
            if (frameIndex === frameCount - 1) {
                nextDelay = pauseDuration;
            }

            // Advance index
            frameIndex = (frameIndex + 1) % frameCount;

            setTimeout(() => {
                requestAnimationFrame(animate);
            }, nextDelay);
        }

        // Start animation
        animate();
    });
</script>
<script>
// Run check continuously
const observer = new MutationObserver(() => {
  document
    .querySelectorAll('.mapboxgl-ctrl-bottom-right')
    .forEach(el => el.classList.add('int_hide'));
});

// Observe the whole document for added DOM nodes
observer.observe(document.body, {
  childList: true,
  subtree: true
});

// Also run once immediately (covers already-present elements)
document
  .querySelectorAll('.mapboxgl-ctrl-bottom-right')
  .forEach(el => el.classList.add('int_hide'));

</script>
</body>
</html>
