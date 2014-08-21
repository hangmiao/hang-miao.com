//7
//Hang Miao
//
//
//Assignment Description: A task management web application using PHP, Ajax, and Scriptaculous library.
//This is the javascript that implements the service.
//
//- animated log in error msg tip
//- add highlight and BlindDown visual effects when adding item
//- add highlight and BlindUp visual effects when deleting item

"use strict";

// level 2 event model
// get session status when loading the page
document.observe("dom:loaded", function() {
    $("logInButton").observe("click", logInSubmit);
    getSessionStatus();
});

// check if user is logged in
function getSessionStatus() {
    var file = "cow.php";
    new Ajax.Request(file,
            {
                parameters: {
                },
                onSuccess: function(ajax) {
                    var response = JSON.parse(ajax.responseText);
                    if (response.userName === null)
                    {
                        // still display log in view
                    }
                    else
                    {
                        // display to-do list view
                        createListView();
                        buildToDoListView(response.userName.toString());
                    }
                },
                onFailure: ajaxFailure,
                onException: ajaxFailure
            }
    );
}

function buildToDoListView(userName) {
    $("main").hide();
    $("listDiv").appear();
    $("h1List").innerHTML = userName + "'s To-Do List";
    createEditLineInToDoList();
    // get to-do list from .json file
    getJSONFile();
}

// create input box line in to-do list view
function createEditLineInToDoList() {
    var inputLine = document.createElement("div");
    inputLine.id = "inputLine";
    // navigate to the listDiv then append before the log out button
    $("h1List").parentNode.insertBefore(inputLine, $("logOutList"));

    // create input box
    var inputBox = document.createElement("input");
    inputBox.id = "inputBox";
    inputBox.type = "text";
    inputBox.size = "30";
    inputBox.maxlength = "30";
    inputLine.appendChild(inputBox);

    // create add to bottom button
    var addToListButton = document.createElement("input");
    addToListButton.id = "addToListButton";
    addToListButton.type = "submit";
    addToListButton.value = "Add to Bottom";
    inputLine.appendChild(addToListButton);
    // set event handler
    $("addToListButton").observe("click", addToBottom);

    // create delete top item button
    var deleteTopItem = document.createElement("input");
    deleteTopItem.id = "deleteTopItem";
    deleteTopItem.type = "submit";
    deleteTopItem.value = "Delete Top Item";
    inputLine.appendChild(deleteTopItem);
    // set event handler
    $("deleteTopItem").observe("click", deleteTop);
}

function addToBottom() {
    // get current list JSON obj
    var cur = getCurrentList();
    // if input only contains empty chars, do nothing
    if ($F("inputBox").trim() !== "") {
        // add it to the end of the array
        // escape HTML special chars
        cur.items.push($F("inputBox").escapeHTML());
        // clear it 
        $("inputBox").clear();
        // print the new list
        printToDoList(cur);
        writeCurrentListToFile(JSON.stringify(cur));

        // add visual effect to the newly added item
        var item = cur.items.length - 1;
        var itemId = "toDoList_" + item;
        new Effect.Highlight($(itemId));
        new Effect.BlindDown($(itemId), {duration: 0.5});
    }
}

function deleteTop() {
    var cur = getCurrentList();
    // add visual effect to the deleted item
    new Effect.Highlight($("toDoList_0"));
    new Effect.BlindUp($("toDoList_0"), {duration: 0.8,
        afterFinish: function() {
            // delete it from the beginning of the array
            cur.items.shift();
            // print the new list
            printToDoList(cur);
            writeCurrentListToFile(JSON.stringify(cur));
        }
    }
    );
}

function writeCurrentListToFile(jsonStr) {
    var file = "cowUpdate.php";
    new Ajax.Request(file,
            {
                parameters: {
                    jsonString: jsonStr
                },
                onSuccess: function(ajax) {
                    if (ajax.responseText === "ERROR") {
                        alert("ERROR when writing to file");
                    }
                },
                onFailure: ajaxFailure,
                onException: ajaxFailure
            }
    );
}

function getCurrentList() {
    var curJson = {};
    curJson["items"] = [];
    // get all the nodes of the list
    if ($("toDoList").hasChildNodes()) {
        var nodeArr = $("toDoList").childNodes;
        for (var i = 0; i < nodeArr.length; i++)
        {
            curJson["items"][i] = nodeArr[i].innerHTML;
        }
    }
    return curJson;
}

// get to-do list from .json file
function getJSONFile() {
    var file = "cowGet.php";
    new Ajax.Request(file,
            {
                parameters: {
                },
                onSuccess: function(ajax) {
                    // new user
                    if (ajax.responseText.indexOf("File created.") > -1)
                    {
                    }
                    else {
                        // print the list from .json file
                        var response = JSON.parse(ajax.responseText);
                        printToDoList(response);
                    }
                },
                onFailure: ajaxFailure,
                onException: ajaxFailure
            }
    );
}

// print the list 
function printToDoList(response) {
    // delete the list if it exists
    if ($("listDiv").contains($("toDoList"))) {
        $("listDiv").removeChild($("toDoList"));
    }
    // create the list dom
    var ul = document.createElement("ul");
    ul.id = "toDoList";
    // insert before "inputLine"
    $("inputLine").parentNode.insertBefore(ul, $("inputLine"));
    // print every item in the array
    for (var i = 0; i < response.items.length; i++) {
        // li must have an id of the form _index to update
        var li = document.createElement("li");
        li.id = "toDoList_" + i;
        li.innerHTML = response.items[i];
        ul.appendChild(li);
    }

    // make it sortable
    Sortable.create("toDoList", {
        onUpdate: function listUpdate() {
            new Effect.Highlight($("toDoList"), {startcolor: '#ffff99', endcolor: '#ffffff'});
            // update the view
            var cur = getCurrentList();
            writeCurrentListToFile(JSON.stringify(cur));
        }
    });
}

function logInSubmit() {
    // remove error msg if it exists
    if (document.body.contains($("logInError"))) {
        document.body.removeChild($("logInError"));
    }
    // remove list view if it exists
    if (document.body.contains($("listDiv"))) {
        document.body.removeChild($("listDiv"));
    }

    createListView();

    var file = "cowLogin.php";
    new Ajax.Request(file,
            {
                method: "post", // optional
                // this will be passed to the target URL
                parameters: {
                    // get two fields' values
                    user: $F("userName"),
                    password: $F("psw")
                },
                onSuccess: function ajaxSuccess(ajax) {
                    var response = JSON.parse(ajax.responseText);
                    var re = response.resp;
                    if (re == "OK")
                    {
                        // if view contains error msg (user input wrong credentials before)
                        if ($("main").previousSibling.id === "logInError") {
                            // remove error msg
                            $("logInError").parentNode.removeChild($("logInError"));
                        }
                        buildToDoListView($F("userName"));
                    }
                    else
                    {
                        if (document.body.contains($("listDiv"))) {
                            document.body.removeChild($("listDiv"));
                        }
                        $("userName").shake();
                        $("psw").shake();
                        logInErrorMsg();
                    }
                },
                onFailure: ajaxFailure,
                onException: ajaxFailure
            }
    );
}

function ajaxFailure(ajax, exception) {
    alert("Error making Ajax request:" +
            "\n\nServer status:\n" + ajax.status + " " + ajax.statusText +
            "\n\nServer response text:\n" + ajax.responseText);
    if (exception) {
        throw exception;
    }
}

// log in error msg
function logInErrorMsg() {
    // when user inputs wrong user name or password more than one time
    if ($("main").previousSibling.id === "logInError") {
        // remove old msg first
        $("logInError").parentNode.removeChild($("logInError"));
        // create new msg
        createErrorMsg();
    }
    else {
        createErrorMsg();
    }
}

// helper function to reduce redundant code when create error msg
function createErrorMsg() {
    var p = document.createElement("p");
    p.innerHTML = "The user name or password is incorrect. Please try again.";
    p.id = "logInError";
    // insert the new heading node
    document.body.insertBefore(p, $("main"));
    p.addClassName("errorMsg");
    p.pulsate({
        duration: 2.0,
        pulses: 2
    });
}

// display to-do list 
function createListView() {
    // create the whole to-do list div to replace the "main" div
    var listDiv = document.createElement("div");
    listDiv.id = "listDiv";
    // here it's not working using $("listDiv").addClassName("todoList");
    listDiv.addClassName("todoList");

    document.body.insertBefore(listDiv, $("main"));
    // create to-do list heading
    var h1List = document.createElement("h1");
    h1List.id = "h1List";
    // insert the new heading node to this new div
    listDiv.appendChild(h1List);

// create log out link
    var logOutList = document.createElement("ul");
    logOutList.id = "logOutList";
    var logOutItem = document.createElement("li");
    logOutList.appendChild(logOutItem);

    var logOutButton = document.createElement("a");
    var linkText = document.createTextNode("Log Out");
    logOutButton.appendChild(linkText);
    logOutButton.id = "logOutButton";
    logOutButton.href = "cow.html";
    $("listDiv").appendChild(logOutList);
    logOutItem.appendChild(logOutButton);
    // set event handler
    $("logOutButton").observe("click", logOutSubmit);
}

// log out
function logOutSubmit() {
    // prevent sending request before server replies
    $("inputBox").disabled = true;
    $("addToListButton").disabled = true;
    $("deleteTopItem").disabled = true;

    var file = "cowLogout.php";
    new Ajax.Request(file,
            {
                method: "post", // optional
                // this will be passed to the target URL
                parameters: {
                },
                onSuccess: function ajaxSuccess(ajax) {
                    $("listDiv").hide();
					$("userName").clear();
                    $("psw").clear();
					
                    $("main").style.display = '';

                    $("inputBox").disabled = false;
                    $("addToListButton").disabled = false;
                    $("deleteTopItem").disabled = false;
                },
                onFailure: ajaxFailure,
                onException: ajaxFailure
            }
    );
}