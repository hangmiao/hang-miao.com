//5 ASCIImation
//Hang Miao
//
//
//Assignment Description: 
//
//This is the JS file for the UI controls.

"use strict";

window.onload = initialWindow;

// initial whole strings of each animation type
var initialWholeText = "";
// store splitted frames
var frameArray = null;
// global frame number
var frameNum = 0;
// intervals between frames
var interval = 250;
//timer id used to cancel timers
var timerID = 0;




function initialWindow() {
//use on "mousedown" instead of "onchange" to fix this bug:
//when first choose blank and type sth., then hit blank again, the things typed are still displayed
    $("animationChoice").onmousedown = selectAnimation;
    $("fontChoice").onchange = selectFontSize;
    $("start").onclick = start;
    $("stop").onclick = stop;
    $("stop").disabled = true;
    $("turbo").checked = false;
    $("turbo").onchange = changeSpeed;
}



function $(elementId) {
    return document.getElementById(elementId);
}


// split the whole long strings
function splitFrame() {
    initialWholeText = $("textArea").value;
    frameArray = initialWholeText.split("=====\n");

}

function displayFrame() {
    // when reach the end of the array, go back to the beginning
    $("textArea").value = frameArray[frameNum % frameArray.length];
    frameNum++;
}

function start() {
    timerID = 0;
    if (frameNum === 0) {
        splitFrame();
    }
    timerID = setInterval(displayFrame, interval);
    // control settings
    $("start").disabled = true;
    $("stop").disabled = false;
    $("animationChoice").disabled = true;

}

function stop() {
    clearInterval(timerID);
    // reset global frame number
    frameNum = 0;
    // control settings
    $("start").disabled = false;
    $("stop").disabled = true;
    $("animationChoice").disabled = false;
    // set text before animation began to display
    $("textArea").value = initialWholeText;
}

function selectAnimation() {
// assign corresponding strings in animations.js, e.g. JUGGER to textarea
    $("textArea").value = ANIMATIONS[$("animationChoice").value];
}

function selectFontSize() {
    // assign css class name to it, e.g. "tiny"
    $("textArea").className = $("fontChoice").value;
}

function changeSpeed() {
    if ($("turbo").checked === true) {
        interval = 50;
    }
    else {
        interval = 250;
    }

// make sure when it's idle, hitting turbo won't cause playing 
    if (frameNum !== 0) {
// clear the new timer when turbo gets hit
        clearInterval(timerID);
        timerID = setInterval(displayFrame, interval);
    }
}

 