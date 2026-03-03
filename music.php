<?php
$dir = "./music/";
$files = array();
$audioFormats = ['oga'];

// Scan directory and collect audio files
if (is_dir($dir)) {
    foreach (scandir($dir) as $file) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), $audioFormats)) {
            $files[] = $dir . $file;
        }
    }
}

// Encode files array as JSON for JavaScript
$filesJson = json_encode($files);
?>

<html lang="en">
<body>
<audio id="player" autoplay></audio>

<script>
let audioFiles = <?php echo $filesJson; ?>;
function shuffle(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
}
audioFiles = shuffle(audioFiles);
let player = document.getElementById('player');
let currentIndex = 0;
function playTrack(index) {
    player.src = audioFiles[index];
    player.play();
}
player.addEventListener('ended', () => {
    currentIndex = (currentIndex + 1) % audioFiles.length;
    playTrack(currentIndex);
});
if (audioFiles.length > 0) {
    playTrack(currentIndex);
}
</script>
<script>
// Should probably make sure that everything has been loaded.
window.addEventListener('load', () => {
  // Now then, when the user clicks anywhere on the page...
  document.addEventListener('click', () => {
    // We go through all the <audio autoplay> elements...
    for (const audio of document.querySelectorAll('[autoplay]')) {
      audio.play() // And finally bless their ears!
    }
  }, { passive: true, once: true }) // `once` for making this only fire once.
}, { passive: true }) // `passive` for some slight optimisation magic.
</script>
</body>
</html>
