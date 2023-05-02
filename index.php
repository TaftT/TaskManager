<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="google-site-verification" content="iPkzX0-yvkCbEPYeU13EzxYL_WJKJa5lse9ud9wU-60" />
  <title>Task app</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
<script src="https://apis.google.com/js/api.js"></script>
</head>
<body>
  <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
  <div id="taskApp">





<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Schedule</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="taskDueDate">Due Date:</label>
          <input v-model="dueDate" class="form-control" type="datetime-local" id="taskDueDate">
        </div>

        <div class="form-group">
          <label for="taskStartDate">Start Date:</label>
          <input v-model="start" class="form-control" type="datetime-local" id="taskStartDate">
        </div>

        <div class="form-group">
          <label for="taskEndDate">End Date:</label>
          <input v-model="end" class="form-control" type="datetime-local" id="taskEndDate">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click="dateSave">Done</button>
      </div>
    </div>
  </div>
</div>

    <main class="wrapper">
      <div class="google">
        <!--Add buttons to initiate auth sequence and sign out-->

      <pre id="content" style="white-space: pre-wrap;"></pre>
      <button class="btn btn-primary" onclick="authenticate().then(loadClient)">Authorize Google Connection</button>
      </div>

      <div id="newTask">
        <form onsubmit="return false">
          <div class="form-group">
            <label for="taskTitle">Title</label>
            <input v-model="task.title" type="text" class="form-control" id="taskTitle" placeholder="Title" required>
          </div>
          <div class="form-group">
            <p>
              <a v-on:click="toggleNewtask" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                More options
              </a>
              <button v-on:click="addTask" class="btn btn-primary">Submit</button>
            </p>
            <div class="collapse" id="collapseExample">
              <div class="card card-body">
                <div class="form-group priorityLvl">
                  <h3>Priority Lvl: {{task.priorityLvl}}</h3>
                </div>
                <div class="form-group">
                  <label for="taskDescription">Description</label>
                  <textarea v-model="task.description" class="form-control" id="taskDescription" rows="3"></textarea>
                </div>

                <div class="form-group">
                  <label class="coolLable" v-bind:style="{
                    backgroundColor: task.color
                  }">Category</label>
                  <select v-model="task.category" class="form-control">
                    <option v-for="cat in catigories">{{cat.name}}</option>
                  </select>
                </div>

                <div class="form-group">
                  <label>Background color: </label>
                  <input type="color" v-model="task.color">
                </div>



                <!-- <div class="form-group">
                  <label for="importanceLvl">Importance: {{task.importanceLvl}}</label>
                  <input v-model="task.importanceLvl" type="range" min="0" max="10" class="slider" id="importanceLvl">
                </div>

                <div class="form-group">
                  <label for="hoursToComplete">Hours until Complete</label>
                  <input v-model="task.hoursToComplete" type="number" class="form-control" id="hoursToComplete" placeholder="hours">
                </div> -->

                <div class="form-group">
                  <div class="slidecontainer">
                    <label for="difficultyLvl">Difficulty: {{task.difficultyLvl}}</label>
                    <input v-model="task.difficultyLvl" type="range" min="0" max="10" class="slider" id="difficultyLvl">
                  </div>
                </div>

                <div class="form-group">
                  <label for="taskDueDate">Due Date:</label>
                  <input v-model="task.dueDate" class="form-control" type="datetime-local" id="taskDueDate">
                </div>

                <div class="form-group">
                  <label for="taskStartDate">Start Date:</label>
                  <input v-model="task.start" class="form-control" type="datetime-local" id="taskStartDate">
                </div>

                <div class="form-group">
                  <label for="taskEndDate">End Date:</label>
                  <input v-model="task.end" class="form-control" type="datetime-local" id="taskEndDate">
                </div>

              </div>
            </div>
          </div>
        </form>
      </div>

      <div id="taskList" v-if="viewUNSheduled">
        <h3>Unscheduled Tasks:</h3>
        <details v-on:click="detailClick(t)" v-for="t in tasks" v-if="t.completion < 10 && t.start == '' && t.end == ''">
            <summary v-bind:style="{
              backgroundColor: t.color
            }">
              <span>{{t.title}}</span>
              <span>Priority:{{priorityCalc(t)}}</span>
                <div v-on:click="completeBtn(t)" v-bind:class="{ taskCompleted: t.completion >= 10 }" class="completeButton"></div>
            </summary>
            <form onsubmit="return false">
              <div class="form-group">
                <label>Title</label>
                <input v-model="t.title" type="text" class="form-control" placeholder="Title" required>
              </div>

              <div class="form-group">
                <label>Description</label>
                <textarea v-model="t.description" class="form-control" rows="3"></textarea>
              </div>

              <div class="form-group">
                <label class="coolLable" v-bind:style="{
                  backgroundColor: t.color
                }">Category</label>
                <select v-model="t.category " class="form-control">
                  <option v-for="cat in catigories">{{cat.name}}</option>
                </select>
              </div>

              <div class="form-group">
                <label>Background color: </label>
                <input type="color" v-model="t.color">
              </div>


              <!-- <div class="form-group">
                <label >Importance: {{task.importanceLvl}}</label>
                <input v-model="t.importanceLvl" type="range" min="0" max="10" class="slider">
              </div>

              <div class="form-group">
                <label>Hours until Complete</label>
                <input v-model="t.hoursToComplete" type="number" class="form-control" placeholder="hours">
              </div> -->

              <div class="form-group">
                <div class="slidecontainer">
                  <label>Difficulty: {{t.difficultyLvl}}</label>
                  <input v-model="t.difficultyLvl" type="range" min="0" max="10" class="slider">
                </div>
              </div>

              <div class="form-group">
                <p>Due Date:{{t.dueDate}}</p>
              </div>

              <div class="form-group">
                <p>Start Date:{{t.start}}</p>
              </div>

              <div class="form-group">
                <p>End Date:{{t.end}}</p>
              </div>
              <!-- Button trigger modal -->
              <div class="form-group">
                <button type="button" v-on:click="selectedTaskid = t.taskid" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
                  Change Dates
                </button>
              </div>
              <button type="submit" class="btn btn-primary" v-on:click="updateTask(t)">Save</button>
              <button type="submit" class="btn btn-danger" v-on:click="deleteTaskFromDB(t)">Delete</button>
            </form>
        </details>
      </div>

      <div class="poolScheduled" v-if="viewSheduled">
        <h3>Scheduled Tasks:</h3>
        <details v-on:click="detailClick(t)" v-for="t in tasks" v-if="t.completion < 10 && t.start && t.end">
            <summary v-bind:style="{
              backgroundColor: t.color
            }">
              <span>{{t.title}}</span>
              <span>Priority:{{priorityCalc(t)}}</span>
              <div v-on:click="completeBtn(t)" v-bind:class="{ taskCompleted: t.completion >= 10 }" class="completeButton"></div>
            </summary>
            <form onsubmit="return false">
              <div class="form-group">
                <label>Title</label>
                <input v-model="t.title" type="text" class="form-control" placeholder="Title" required>
              </div>

              <div class="form-group">
                <label>Description</label>
                <textarea v-model="t.description" class="form-control" rows="3"></textarea>
              </div>

              <div class="form-group">
                <label class="coolLable" v-bind:style="{
                  backgroundColor: t.color
                }">Category</label>
                <select v-model="t.category " class="form-control">
                  <option v-for="cat in catigories">{{cat.name}}</option>
                </select>
              </div>

              <div class="form-group">
                <label>Background color: </label>
                <input type="color" v-model="t.color">
              </div>



              <!-- <div class="form-group">
                <label >Importance: {{task.importanceLvl}}</label>
                <input v-model="t.importanceLvl" type="range" min="0" max="10" class="slider">
              </div>

              <div class="form-group">
                <label>Hours until Complete</label>
                <input v-model="t.hoursToComplete" type="number" class="form-control" placeholder="hours">
              </div> -->

              <div class="form-group">
                <div class="slidecontainer">
                  <label>Difficulty: {{t.difficultyLvl}}</label>
                  <input v-model="t.difficultyLvl" type="range" min="0" max="10" class="slider">
                </div>
              </div>

              <div class="form-group">
                <p>Due Date:{{t.dueDate}}</p>
              </div>

              <div class="form-group">
                <p>Start Date:{{t.start}}</p>
              </div>

              <div class="form-group">
                <p>End Date:{{t.end}}</p>
              </div>
              <!-- Button trigger modal -->
              <div class="form-group">
                <button type="button" v-on:click="selectedTaskid = t.taskid" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
                  Change Dates
                </button>
              </div>

              <button type="submit" class="btn btn-primary" v-on:click="updateTask(t)">Save</button>
              <button type="submit" class="btn btn-danger" v-on:click="deleteTaskFromDB(t)">Delete</button>
            </form>
        </details>

      </div>

      <div class="completedPool">
        <h3>Completed Tasks:</h3>
        <details v-on:click="detailClick(t)" v-for="t in tasks" v-if="t.completion >= 10 ">
            <summary v-bind:style="{
              backgroundColor: t.color
            }">
              <span>{{t.title}}</span>
              <span>Priority:{{priorityCalc(t)}}</span>
              <div v-on:click="copyTaskToDB(t)"class="completeButton backgroundGrey"></div>
              <div v-on:click="completeBtn(t)" v-bind:class="{ taskCompleted: t.completion >= 10 }" class="completeButton"></div>
            </summary>
            <form onsubmit="return false">
              <div class="form-group">
                <label>Title</label>
                <input v-model="t.title" type="text" class="form-control" placeholder="Title" required>
              </div>

              <div class="form-group">
                <label>Description</label>
                <textarea v-model="t.description" class="form-control" rows="3"></textarea>
              </div>

              <div class="form-group">
                <label class="coolLable" v-bind:style="{
                  backgroundColor: t.color
                }">Category</label>
                <select v-model="t.category " class="form-control">
                  <option v-for="cat in catigories">{{cat.name}}</option>
                </select>
              </div>

              <div class="form-group">
                <label>Background color: </label>
                <input type="color" v-model="t.color">
              </div>

              <!-- <div class="form-group">
                <label >Importance: {{task.importanceLvl}}</label>
                <input v-model="t.importanceLvl" type="range" min="0" max="10" class="slider">
              </div>

              <div class="form-group">
                <label>Hours until Complete</label>
                <input v-model="t.hoursToComplete" type="number" class="form-control" placeholder="hours">
              </div> -->

              <div class="form-group">
                <div class="slidecontainer">
                  <label>Difficulty: {{t.difficultyLvl}}</label>
                  <input v-model="t.difficultyLvl" type="range" min="0" max="10" class="slider">
                </div>
              </div>

              <div class="form-group">
                <p>Due Date:{{t.dueDate}}</p>
              </div>

              <div class="form-group">
                <p>Start Date:{{t.start}}</p>
              </div>

              <div class="form-group">
                <p>End Date:{{t.end}}</p>
              </div>

              <button type="submit" class="btn btn-primary" v-on:click="updateTask(t)">Save</button>
              <button type="submit" class="btn btn-danger" v-on:click="deleteTaskFromDB(t)">Delete</button>
            </form>
        </details>

      </div>


    </main>
  </div>
  <script src="app.js"></script>
  <script async defer src="https://apis.google.com/js/api.js"
      onload="this.onload=function(){};handleClientLoad()"
      onreadystatechange="if (this.readyState === 'complete') this.onload()">
    </script>
</body>
</html>
