function getDataFromServer() {
  return new Promise((resolve, reject)=> {

    const xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", "server.php");
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send();
    xmlhttp.onload = ()=>{
      console.log(xmlhttp.status)
      var txt = xmlhttp.response+"";
      // console.log(txt)
      if(xmlhttp.status == 200){
        console.log("success")
        var j = JSON.parse(txt);
        taskApp.tasks = j;
        resolve(true)
      } else {
        console.log(txt)
        taskApp.error=txt;
        reject(false);
      }
    }

  });

}

function copyTaskOnServer(task) {
  return  new Promise((resolve, reject)=> {
    var taskClone = JSON.parse(JSON.stringify(task));
    taskClone.completion = 0;
    taskClone.start = "";
    taskClone.end = "";
    var newTask = JSON.stringify(taskClone);
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.open("POST", "server.php");
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("newTask=" + newTask);
    xmlhttp.onload = ()=>{
      console.log(xmlhttp.status)
      var txt = xmlhttp.response+"";
      if(xmlhttp.status == 201){
        console.log("success")
        resolve(true)
      } else {
        console.log(txt)
        taskApp.error=txt;
        reject(false);
      }
    }

  });
}



var taskApp = new Vue({
  el: '#taskApp',
  data: {
    text: 'Hello Vue.js!',
    error:"",
    start:"",
    end:"",
    dueDate:"",
    selectedTaskid:-1,
    viewSheduled:true,
    viewUNSheduled:true,
    selectedCat:"Misc.",
    catigories:[
      {name:"Misc.",importance:0, color:"RoyalBlue"},
      {name:"Work",importance:8, color:"Salmon"},
      {name:"School/Skill",importance:9, color:"LemonChiffon"},
      {name:"Church",importance:6, color:"IndianRed"},
      {name:"Chore",importance:5, color:"SkyBlue"},
      {name:"Finance",importance:8, color:"SeaGreen"},
    ],
    newTaskPage:false,
    task:{
      title:"",
      description:"",
      category:"Misc.",
      dueDate:"",
      difficultyLvl:0,
      completion:0,
      start:"",
      end:"",
      priorityLvl:1,
      color:"#4169e1",
    },
    tasks:[ ]
  },
  methods: {
    toggleNewtask: function (task) {
      if  (this.newTaskPage){
        this.newTaskPage = false
      } else {
        this.newTaskPage = true
      }
    },
    sortTasks: function () {
      this.tasks.sort((a,b)=>(a.priorityLvl< b.priorityLvl) ? 1:-1)
    },
    completeBtn: function (task) {
      if  (task.completion < 10){
        task.completion = 10
      } else {
        task.completion = 0
      }
      this.updateTask(task)
      // this.viewSheduled=false;
      // this.viewUNSheduled=false;
      // this.viewSheduled=true;
      // this.viewUNSheduled=true;
    },
    addTask: function () {
      if(this.task.start && !this.task.end){
        return
      }
      if(!this.task.start && this.task.end){
        return
      }
      if(this.task.title){
        if(this.task.end && this.task.start){
          this.task.end = this.dateFormat(this.task.end)
          this.task.start = this.dateFormat(this.task.start)
        }
        if(this.task.dueDate){
          this.task.dueDate = this.dateFormat(this.task.dueDate)
        }

        this.tasks.push(this.task)

        this.saveTaskToDB()
        if(this.task.start && this.task.end){
          execute(this.task)
        }
      }
    },
    priorityCalc: function (task) {
      var lvl = 1;
      for (var i = 0; i < this.catigories.length; i++) {
        if (this.catigories[i].name == task.category){
          lvl += this.catigories[i].importance
          break;
        }
      }

      if(parseInt(task.difficultyLvl)>0){
        lvl += parseInt(task.difficultyLvl);
      }

      // if(task.hoursToComplete>10){
      //   lvl += 10;
      // } else {
      //   lvl += Math.floor(parseInt(task.hoursToComplete))
      // }
      //

      //
      // if(parseInt(task.importanceLvl)>0){
      //   lvl *= parseInt(task.importanceLvl);
      // }

      if(task.dueDate){
        var currentDateSeconds = Math.floor(new Date().getTime() / 1000);
        var dueDateSeconds = new Date(task.dueDate).getTime() / 1000;
        var timeDiff = dueDateSeconds - currentDateSeconds
        // console.log(currentDateSeconds)
        // console.log(dueDateSeconds)
        // console.log(timeDiff)
        if(timeDiff < 86400){
          // day
          lvl *= 10;

        } else if(timeDiff < 604800){
          // 1 week
          lvl *= 8;

        } else if(timeDiff < 1209600){
          // 2 weeks
          lvl *= 7;

        }else if(timeDiff < 1814400){
          // 3 weeks
          lvl *= 5;

        } else if(timeDiff < 2419200){
          // 4 week
          lvl *= 4;

        }  else if(timeDiff < 7257600){
          // 3 months
          lvl *= 3;

        } else if(timeDiff < 31536000){
          // 6 year
          lvl *= 2;

        } else {
          // more than year
          lvl += 0;
        }

      }



      // if(task.completion>0){
      //   lvl = lvl / parseInt(task.completion);
      // }

      // if(task.start && task.end){
      //   lvl = lvl - 2;
      // }

      task.priorityLvl=lvl;
      return lvl



    },
    checkCompleted: function () {
      var currentDateSeconds = Math.floor(new Date().getTime() / 1000);
      this.tasks.forEach((task, i) => {
        if(task.end != ''){
          var endDateSeconds = new Date(task.end).getTime() / 1000;
          var timeDiff = (endDateSeconds+600) - currentDateSeconds
          if (task.completion < 10 && timeDiff<0){
            task.end = "";
            task.start = "";
          }

        }
      });
    },
    updateTask: function (task) {
        var updateTask= JSON.stringify(task);
        const xmlhttp = new XMLHttpRequest();
        xmlhttp.open("POST", "server.php");
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("updateTask=" + updateTask);
        xmlhttp.onload = ()=>{
          console.log(xmlhttp.status)
          var txt = xmlhttp.response+"";
          if(xmlhttp.status == 200){
            console.log("success")
            this.getTasksFromDB()
          } else {
            console.log(txt)
            this.error=txt;
          }
        }

      },
      dateSave: function () {
        console.log(this.selectedTaskid)
        var newStart = this.dateFormat(this.start)
        var newEnd = this.dateFormat(this.end)
        var newDue = this.dateFormat(this.dueDate)
        this.tasks.forEach((t, i) => {
          if(t.taskid == this.selectedTaskid){
            if(newStart && newEnd){
              t.start = newStart;
              t.end = newEnd;
              execute(t)
            } else {
              t.start = "";
              t.end = "";
            }
            if(newDue){
              t.dueDate = newDue;
            } else {
              t.dueDate = "";
            }
            this.updateTask(t)
          }
        });

        this.start="";
        this.end="";
        this.dueDate="";
        this.selectedTaskid=-1;

        },
    dateFormat: function (date) {
        return String(moment(date).format('LLL'))
      },
      detailClick: function (targetDetail) {
        // Fetch all the details element.
          const details = document.querySelectorAll("details");

          // Add the onclick listeners.
          details.forEach((targetDetail) => {
          targetDetail.addEventListener("click", () => {
            // Close all the details that are not targetDetail.
            details.forEach((detail) => {
              if (detail !== targetDetail) {
                detail.removeAttribute("open");
              }
            });
          });
          });
        },

    saveTaskToDB: function(){
      var newTask = JSON.stringify(this.task);
      const xmlhttp = new XMLHttpRequest();
      xmlhttp.open("POST", "server.php");
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xmlhttp.send("newTask=" + newTask);
      xmlhttp.onload = ()=>{
        console.log(xmlhttp.status)
        var txt = xmlhttp.response+"";
        if(xmlhttp.status == 201){
          this.getTasksFromDB()
          console.log("success")
          this.task= {
            title:"",
            description:"",
            category:"Misc.",
            dueDate:"",
            // importanceLvl:0,
            // hoursToComplete:0,
            difficultyLvl:0,
            completion:0,
            start:"",
            end:"",
            priorityLvl:0,
            color:"#4169e1"
          }
        } else {
          console.log(txt)
          this.error=txt;
        }
      }
    },
    copyTaskToDB: function(task){
      copyTaskOnServer(task).then((data)=>{


        this.getTasksFromDB()

      });

    },
    deleteTaskFromDB: function(task){
      new Promise((resolve, reject) => {
        var newTask = JSON.stringify(this.task);
        const xmlhttp = new XMLHttpRequest();
        xmlhttp.open("POST", "server.php");
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("taskid=" + task.taskid);
        xmlhttp.onload = ()=>{
          console.log(xmlhttp.status)
          // var txt = xmlhttp.response+"";
          if(xmlhttp.status == 200){
            console.log("success")
            resolve();
          } else {
            console.log(txt)
            this.error=txt;
          }
        }

      }).then(()=>{
        this.getTasksFromDB()
      })
    },
    getTasksFromDB: function(){
      return getDataFromServer().then((data)=>{
        if(data){
          this.sortTasks()
          console.log("this")


        }
      });
    },
    linkedComplete: function() {

      var queryString = window.location.search;
      var cleanqueryString = queryString.replace('?','');
      var parts = queryString.split("=");
      var taskid = Number(parts[1]);
      console.log(taskid)
      this.tasks.forEach((t, i) => {
        if(t.taskid == taskid){
          t.completion = 10;
          console.log(t)
          this.updateTask(t);
        }
      });

    },
  },
  filters:{
    dateFormat: function (date) {
        return String(moment(date).format('LLL'))
      }
  },

  created: function () {
    this.getTasksFromDB().then(()=>{
      this.linkedComplete()
    })
    
  }
})



setInterval(function(){
  if(taskApp.newTaskPage){
    taskApp.priorityCalc(taskApp.task)


  }
  taskApp.checkCompleted()
}, 1000);

// Client ID and API key from the Developer Console
var CLIENT_ID = '571413124842-ceg6kvit2c03mr0f7ins9l0foap8t513.apps.googleusercontent.com';
var API_KEY = 'AIzaSyC1jCn0JREi78jdNHoYEoB6l7Zca9UIfis';

// Array of API discovery doc URLs for APIs used by the quickstart
var DISCOVERY_DOCS = ["https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest"];

// Authorization scopes required by the API; multiple scopes can be
// included, separated by spaces.
var SCOPES = "https://www.googleapis.com/auth/calendar.readonly";

var authorizeButton = document.getElementById('authorize_button');
var signoutButton = document.getElementById('signout_button');

/**
 *  On load, called to load the auth2 library and API client library.
 */
function handleClientLoad() {
  gapi.load('client:auth2', initClient);
}

/**
 *  Initializes the API client library and sets up sign-in state
 *  listeners.
 */
function initClient() {
  gapi.client.init({
    apiKey: API_KEY,
    clientId: CLIENT_ID,
    discoveryDocs: DISCOVERY_DOCS,
    scope: SCOPES
  }).then(function () {
    // Listen for sign-in state changes.
    gapi.auth2.getAuthInstance().isSignedIn.listen(updateSigninStatus);

    // Handle the initial sign-in state.
    updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
    authorizeButton.onclick = handleAuthClick;
    signoutButton.onclick = handleSignoutClick;
  }, function(error) {
    appendPre(JSON.stringify(error, null, 2));
  });
}

/**
 *  Called when the signed in status changes, to update the UI
 *  appropriately. After a sign-in, the API is called.
 */
function updateSigninStatus(isSignedIn) {
  if (isSignedIn) {
    authorizeButton.style.display = 'none';
    signoutButton.style.display = 'block';
    listUpcomingEvents();
  } else {
    authorizeButton.style.display = 'block';
    signoutButton.style.display = 'none';
  }
}

/**
 *  Sign in the user upon button click.
 */
function handleAuthClick(event) {
  gapi.auth2.getAuthInstance().signIn();
}

/**
 *  Sign out the user upon button click.
 */
function handleSignoutClick(event) {
  gapi.auth2.getAuthInstance().signOut();
}

/**
 * Append a pre element to the body containing the given message
 * as its text node. Used to display the results of the API call.
 *
 * @param {string} message Text to be placed in pre element.
 */
function appendPre(message) {
  var pre = document.getElementById('content');
  var textContent = document.createTextNode(message + '\n');
  pre.appendChild(textContent);
}

/**
 * Print the summary and start datetime/date of the next ten events in
 * the authorized user's calendar. If no events are found an
 * appropriate message is printed.
 */
function listUpcomingEvents() {
  gapi.client.calendar.events.list({
    'calendarId': 'primary',
    'timeMin': (new Date()).toISOString(),
    'showDeleted': false,
    'singleEvents': true,
    'maxResults': 10,
    'orderBy': 'startTime'
  }).then(function(response) {
    var events = response.result.items;
    appendPre('Upcoming events:');

    if (events.length > 0) {
      for (i = 0; i < events.length; i++) {
        var event = events[i];
        var when = event.start.dateTime;
        if (!when) {
          when = event.start.date;
        }
        appendPre(event.summary + ' (' + when + ')')
      }
    } else {
      appendPre('No upcoming events found.');
    }
  });
}

/**
   * Sample JavaScript code for calendar.events.insert
   * See instructions for running APIs Explorer code samples locally:
   * https://developers.google.com/explorer-help/guides/code_samples#javascript
   */


  function authenticate() {
    return gapi.auth2.getAuthInstance()
        .signIn({scope: "https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/calendar.events"})
        .then(function() { console.log("Sign-in successful"); },
              function(err) { console.error("Error signing in", err); });
  }
  function loadClient() {
    gapi.client.setApiKey(API_KEY);
    return gapi.client.load("https://content.googleapis.com/discovery/v1/apis/calendar/v3/rest")
        .then(function() { console.log("GAPI client loaded for API"); },
              function(err) { console.error("Error loading GAPI client for API", err); });
  }
  // Make sure the client is loaded and sign-in is complete before calling this method.
  function execute(task) {
    var url = "";
    if(task.taskid){
      url = 'https://utmathtutor.com/codecamp?taskid='+task.taskid;
    } else {
      url = "https://utmathtutor.com/codecamp";
    }
    return gapi.client.calendar.events.insert({
      'calendarId': 'primary',
      "resource": {
        'description': 'Category: '+task.category + ' | Due: '+task.dueDate + ' | Difficulty: '+task.difficulty+' | '+task.description+' | '+url,
        "summary":task.title,
        // "colorId":"",
        "start": {
          'dateTime': new Date(task.start),
          'timeZone': 'US/Mountain'
        },
        "end": {
          'dateTime': new Date(task.end),
          'timeZone': 'US/Mountain'
        },

      }
    })
        .then(function(response) {
                // Handle the results here (response.result has the parsed body).
                console.log("Response", response);
              },
              function(err) { console.error("Execute error", err); });
  }
  gapi.load("client:auth2", function() {
    gapi.auth2.init({client_id: CLIENT_ID});
  });
