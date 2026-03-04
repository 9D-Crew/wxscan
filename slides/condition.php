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
.slidebg {
    background-image: url('/assets/ccbgs/<?php $randomNumber = rand(1, 7); echo $randomNumber; ?>.png');
    background-repeat: no-repeat;
    background-size: 100% 100%;
    position: absolute;
    font-family: "font";
    color: white;
    top: 0;
    left: 0;
    width: 96%;
    height: 99%;
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
#citytext2 {
    position: absolute;
    top:-18;right:80;
    text-align: right;
}
#bigicon {
    position: absolute;
    top:50;left: 77%;
    transform: translateX(-50%);
}
#bigcc {
    position: absolute;
    top: 160;
    left: 77%;
    transform: translateX(-50%);
    text-align: center;
    white-space: nowrap;
}
#bigtemp {
    position: absolute;
    top: 200;
    left: 77%;
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
#textTitle {
    color: #DECD10;
    position: absolute;
    top: 8%; left: 16%;
}
#textCast {
    color: white;
    position: absolute;
    top: 18%; left: 16%;
    width: 75%; height: 70%;
}
</style>

<div id="slideb" class="slidebg">
<div class="hidden" id="slidebdata">
<h2 class="currentlytext">Currently</h2>
<h2 id="citytext"></h2>
<h2 class="leftside">Humidity<br>Dew Point<br>Pressure<br>Winds<br>Gusts<br></h2>
<h2 id="leftsidedata"></h2>
<div class="slidebbar"></div>
<img src="/assets/icon/0.webp" id="bigicon">
<h2 id="bigcc"></h2>
<h2 id="bigtemp">999</h2>
</div>
</div>

<div id="slidec" class="slidebg hidden">
<h2 class="currentlytext" >Local Forecast</h2>
<h2 id="citytext2"></h2>
<h2 id="textTitle"></h2>
<h2 id="textCast"></h2>
</div>

<script>
function textForecast(data) {
    let target;
    target = document.getElementById("slideb");
    target.classList.add('hidden')
    target = document.getElementById("slidec");
    target.classList.remove('hidden')
    function update(data, go) {
        let target;
        target = document.getElementById("textTitle");
        target.innerText = data.extended.daypart[go].name;
        target = document.getElementById("textCast");
        target.innerText = data.extended.daypart[go].narration;
    }
    update(data, 0)
    setTimeout(update, 6000, data, 1);
    setTimeout(update, 12000, data, 2);
    setTimeout(function() {window.parent.postMessage('slideDone', '*');}, 18000);
}

function setCityText(params){
    let city = params.slice(1, -3);
    city = decodeURIComponent(city);
    city = city.toLowerCase();
    city = city.replace(/\b\w/g, function(char) {
        return char.toUpperCase();
    });
    let bah = document.getElementById("citytext");
    bah.innerText = city;
    bah = document.getElementById("citytext2");
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
        setTimeout(textForecast, 10000, data);
    })
}

var params = window.location.search;
setCityText(params);
getdata(params)
</script>