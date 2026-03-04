<style>
@font-face {
    font-family: "font";
    src: url("/assets/fonts/inter_reg.ttf") format("truetype"),
}
@font-face {
    font-family: "font_bold";
    src: url("/assets/fonts/inter_bold.ttf") format("truetype"),
}
.hidden {
    display: none;
}
#slideb {
    position: absolute;
    font-family: "font";
    color: white;
    top: 0;
    left: 0;
    background-image: url('/assets/ccbgs/<?php $randomNumber = rand(1, 7); echo $randomNumber; ?>.png');
    width: 96%;
    height: 99%;
    background-repeat: no-repeat;
    background-size: 100% 100%;
}
.currentlytext {
    position: absolute;
    top:-19;left:10;
}
#citytext {
    position: absolute;
    top:-18;right:80;
    text-align: right;
}
#bigicon {
    position: absolute;
    top:50;right:65;
}
#bigcc {
    position: absolute;
    top: 160;
    left: 74%;
    transform: translateX(-50%);
    text-align: center;
    white-space: nowrap;
}
#bigtemp {
    position: absolute;
    top: 200;
    left: 74%;
    transform: translateX(-50%);
    text-align: center;
}
.leftside {
    color: #DECD10;
    position: absolute;
    line-height: 1.6;
    top: 20;
    left: 15%;
}
#leftsidedata {
    color: white;
    position: absolute;
    line-height: 1.6;
    top: 20;
    right: 41%;
    text-align: right;
}
.slidebbar {
    position: absolute;
    top: 33;
    right: 39%;
    background-color: #0A105B;
    width: 5px;
    height: 88.2%;
}
</style>

<div id="slideb">
<div class="hidden" id="slidebdata">
<h2 class="currentlytext">Currently</h2>
<h2 id="citytext"></h2>

<!-- left side -->
<h2 class="leftside">
Humidity<br>
Dew Point<br>
Pressure<br>
Winds<br>
Gusts<br>
</h2>

<div class="slidebbar"></div>

<h2 id="leftsidedata">
100%<br>
999<br>
30.24 R<br>
calm<br>
none<br>
</h2>

<!-- right side -->
<img src="/assets/icon/0.webp" id="bigicon">
<h2 id="bigcc"></h2>
<h2 id="bigtemp">999</h2>
</div>
</div>


<script>
setTimeout(function() {window.parent.postMessage('slideDone', '*');}, 10000);

function setCityText(params){
    let city = params.slice(1, -3);
    city = decodeURIComponent(city);
    city = city.toLowerCase();
    city = city.replace(/\b\w/g, function(char) {
        return char.toUpperCase();
    });
    const bah = document.getElementById("citytext");
    bah.innerText = city;
}

function getdata(params) {
    // ravioli ravioli give me the datauoli
    fetch("/data.php?loc=" + params)
    .then(response => response.json())
    .then(data => {
        let target;
        // phrase
        target = document.getElementById("bigcc");
        target.innerText = data.current.info.phraseMedium;
        // temp
        target = document.getElementById("bigtemp");
        target.innerText = data.current.conditions.temperature;
        // icon
        target = document.getElementById("bigicon");
        target.src = "/assets/icon/" + data.current.info.iconCode + ".webp";
        // left side
        target = document.getElementById("leftsidedata");
        let cooldata = [];
        cooldata.push(data.current.conditions.humidity + "%");
        cooldata.push(data.current.conditions.dewPoint);
        cooldata.push(data.current.conditions.pressure + " " + data.current.conditions.pressureTendencyPhrase[0]);
        cooldata.push(data.current.conditions.windCardinal + " " + data.current.conditions.windSpeed);
        cooldata.push((data.current.conditions.windGusts !== null ? data.current.conditions.windGusts : "none"));
        target.innerHTML = cooldata.join("<br>");
        // data is done, unhide the data
        target = document.getElementById("slidebdata");
        target.classList.remove('hidden')
    })
}

var params = window.location.search;
setCityText(params);
getdata(params)
</script>