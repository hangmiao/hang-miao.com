//6 Fifteen Puzzle
//Hang Miao
//
//
//Assignment Description: JavaScript's Document Object Model (DOM) and events
//
//This is the JS file for the event controls.

"use strict";

// number of rows/cols of the puzzle
var AREA_SIZE = 4;
// each tile's width/height
var TILE_SIZE = 100;
// empty tile's row number
var emptyRowNum = 3;
// empty tile's col number
var emptyColNum = 3;

// unobtrusive
window.onload = initialWindow;

// set up the puzzle
function initialWindow() {
// get all the puzzle pieces by using the CSS selectors
    var puzzlePiecesArray = document.querySelectorAll("#puzzlearea div");

// assign all tiles css ids, class and initial positions
    for (var i = 0; i < puzzlePiecesArray.length; i++)
    {
        // get current tile's row number
        var rowNum = Math.floor(i / AREA_SIZE);
        // get current tile's col number
        var colNum = i % AREA_SIZE;

        // assign corresponding css class to every tile        
        puzzlePiecesArray[i].className = "puzzlepiece";
        // give each tile an id for future access
        puzzlePiecesArray[i].id = "tile_" + rowNum + "_" + colNum;

        // assign position to each tile
        puzzlePiecesArray[i].style.top = rowNum * TILE_SIZE + "px";
        puzzlePiecesArray[i].style.left = colNum * TILE_SIZE + "px";

        // assign background position to the tiles
        puzzlePiecesArray[i].style.backgroundPosition = -colNum * TILE_SIZE + "px " + -rowNum * TILE_SIZE + "px";

        puzzlePiecesArray[i].onclick = moveTile;
        puzzlePiecesArray[i].onmouseover = hoverOver;
        puzzlePiecesArray[i].onmouseout = revertState;
    }
    
    $("shufflebutton").onclick = shufflling;
}

// give row number and col number to get the correponding tile object
function getElementByRowColNum(rNum, cNum) {
    // make the id string
    var tileId = "tile_" + rNum.toString() + "_" + cNum.toString();
    // return the obj
    return $(tileId);
}

// give an obj to get its row number
function getRowNumByObj(currentObj) {
    // temp for debugging
    var temp = parseInt(currentObj.style.top) / TILE_SIZE;
    return temp;
}

// give an obj to get its row number
function getColNumByObj(currentObj) {
    var temp = parseInt(currentObj.style.left) / TILE_SIZE;
    return temp;
}

// add the css class when the tile can be moved
function hoverOver() {
    // if the tile can be moved, hightlight it
    // otherwise, nothing happens
    if (checkIsMovable(this)) {
        this.className = "puzzlepiece movablepiece";
    }
}

// remove the css class when the mouse is no longer hovering
function revertState() {
    this.className = "puzzlepiece";
}

// move a particular tile
function moveTile() {
    if (checkIsMovable(this))
    {
        move(this);
        if (isWon()){
            alert("Congrats, you won!");
        }
    }
}

// swap movable tile and the empty tile
function move(curObj) {
    var curRNum = getRowNumByObj(curObj);
    var curCNum = getColNumByObj(curObj);

// set the destination position of current obj  (obj that bound with the event handler)
    curObj.style.top = emptyRowNum * TILE_SIZE + "px";
    curObj.style.left = emptyColNum * TILE_SIZE + "px";

    // keep track of empty row/col number
    emptyRowNum = curRNum;
    emptyColNum = curCNum;
}

// check if a tile is movable or not
function checkIsMovable(currentObj) {
    var curRNum = getRowNumByObj(currentObj);
    var curCNum = getColNumByObj(currentObj);
    
// if the current tile is in the up/down/left/top direction of the empty tile, then it can be moved
    var sameRowBool = Math.abs(curRNum - emptyRowNum) === 1 && curCNum === emptyColNum;
    var sameColBool = Math.abs(curCNum - emptyColNum) === 1 && curRNum === emptyRowNum;
    if (sameRowBool || sameColBool){
        return true;
		}
    else{
        return false;
		}
}

function shufflling() {
    for (var i = 0; i < 300; i++)
    {
        shuffleOnce();
    }
}

function shuffleOnce() {
    // store movable pieces of a particular tile
    var movablePiecesArray = [];

// get all the movable pieces for a particular tile and store them in the movable array
    var puzzlePiecesArray = document.querySelectorAll("#puzzlearea div");
    for (var i = 0; i < puzzlePiecesArray.length; i++)
    {
        if (checkIsMovable(puzzlePiecesArray[i])) {
            // add movable piece obj to the array
            movablePiecesArray.push(puzzlePiecesArray[i]);
        }
    }
    // index of movalbe piece that gets chosen
    var whichChosen = parseInt(Math.random() * movablePiecesArray.length);
    // move this obj 
    move(movablePiecesArray[whichChosen]);
}

function isWon(){
    // get all the current pieces
    var puzzlePiecesArray = document.querySelectorAll("#puzzlearea div");
    for (var i = 0; i < puzzlePiecesArray.length; i++)
    { 
    var rowBool = Math.floor(i/AREA_SIZE) !== getRowNumByObj(puzzlePiecesArray[i]);
    var colBool = i%AREA_SIZE !== getColNumByObj(puzzlePiecesArray[i]);
       if( colBool || rowBool)
       {
           return false;
       }
    }
    return true;
}