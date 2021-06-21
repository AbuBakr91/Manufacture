<template>
    <div>
        <app-header></app-header>
        <div class="container-fluid">
            <div class="row">
                <div class="col-3 mt-3 left_block">
                    <div class="row mt-3">
                        <div class="col-12 avatar text-center">
                            <img src="img/avatar.png" width="150" alt="avatar">
                        </div>
                        <div class="col-12 text-center">
                            <h4><b>{{user.firstname}} {{user.lastname}}</b></h4>
                        </div>
                        <div class="col-12 text-center">
                            <h5>Должность:</h5>
                            <h6>{{printRole(user.slug)}}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-9">
                    <div class="row mt-5 content_start" v-if="!start">
                        <div class="col-8">
                            <h4 class="text-center">Задание от руководителя:</h4>
                            <select class="form-control form-control-lg" v-model="card">
                                <option value="selected">Выберите тех.карту</option>
                                <option v-for="task in taskManager" :value="task.id">{{task.name}} {{task.user_count}}</option>
                            </select>
                        </div>
                        <div class="col-8 mt-3">
                            <h4 class="text-center">Выбрать произвольно:</h4>
                            <select :disabled="taskManager.length !== 0" class="form-control form-control-lg">
                                <option>Выберите тех.карту</option>
                                <option>LV-1-hand</option>
                                <option>Relay-4S-Case</option>
                            </select>
                        </div>
                        <div class="col-8 mt-5 d-flex justify-content-center">
                            <button class="btn primary btn_start" @click="startWork" type="submit">START</button>
                        </div>
                    </div>
                    <div class="row mt-5" v-if="start">
                        <h2 class="text-center mb-5">Название тех.карты: <b>{{currentTask[0].name}} Количество: {{currentTask[0].user_count}}</b></h2>
                        <div class="col-8 m-auto content_stop mt-5">
                            <button class="btn btn_pause" :disabled="waiting" @click="addPaused" v-html="pause ? pauseIcon : 'PAUSE'" type="submit"></button>
                            <button class="btn btn_pause" :disabled="pause" @click="addWaiting" v-html="waiting ? pauseIcon : 'Ожидание'" type="submit"></button>
                        </div>
                        <div class="col-12 d-flex justify-content-center mt-5">
                            <button class="btn btn_stop float-center" :disabled="waiting || pause" @click="stopFuture" type="submit">STOP</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <app-modal v-if="modal" :task="currentTask[0].name" @close="modal = false"></app-modal>
</template>

<script>
import store from "../store"
import AppHeader from "../Components/AppHeader";
import AppModal from "../components/AppModal";

export default {
    data() {
        return {
            card: 'selected',
            modal: false,
            start: false,
            pause: false,
            waiting: false,
            currentTask: [],
            dataTime: '',
            taskManager: [],
            user: JSON.parse(store.state.auth.user),
            role: store.state.auth.role,
            pauseIcon: "<svg fill='#fff' height='20px' id='Layer_1' style='enable-background:new 0 0 512 512;' version='1.1' viewBox='0 0 512 512' width='20px' xml:space='preserve' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'><g><path d='M224,435.8V76.1c0-6.7-5.4-12.1-12.2-12.1h-71.6c-6.8,0-12.2,5.4-12.2,12.1v359.7c0,6.7,5.4,12.2,12.2,12.2h71.6   C218.6,448,224,442.6,224,435.8z'/><path d='M371.8,64h-71.6c-6.7,0-12.2,5.4-12.2,12.1v359.7c0,6.7,5.4,12.2,12.2,12.2h71.6c6.7,0,12.2-5.4,12.2-12.2V76.1   C384,69.4,378.6,64,371.8,64z'/></g></svg>"
        }
    },
    methods: {
        getNow() {
            this.dataTime = new Date().toLocaleTimeString()
        },
        printRole(slug) {
          if (slug === 'collector') {
            return 'Сборщик'
          } else if(slug === 'machine-operator') {
            return 'Оператор станка'
          }
            return 'Пайщик'
        },
        async getTask() {
            const tasks = await axios.get('api/user_task/' + this.user.department_id)
            this.taskManager.push(...tasks.data)
        },
        async addPaused() {
            if (!this.pause) {
                const paused = await axios.post('api/add-paused/', {
                    begin: true,
                    work_id: this.currentTask[0].id
                })

                this.pause = true
            } else {
                const paused = await axios.post('api/add-paused/', {
                    finish: true,
                    work_id: this.currentTask[0].id
                })

                this.pause = false
            }
        },
        async addWaiting() {
            if (!this.waiting) {
                const waiting = await axios.post('api/add-waiting/', {
                    begin: true,
                    work_id: this.currentTask[0].id
                })

                this.waiting = true
            } else {
                const waiting = await axios.post('api/add-waiting/', {
                    finish: true,
                    work_id: this.currentTask[0].id
                })

                this.waiting = false
            }
        },
        async stopFuture() {
            this.start = !this.start
            this.modal = !this.modal
        },
        async startStatus() {
            const start = await axios.get('api/task-status/' + this.user.id)
            if (start.data.start) {
                this.start = true
            } else {
                this.start = false
            }

            if (start.data.paused) {
                this.pause = true
            } else {
                this.pause = false
            }

            if (start.data.waiting) {
                this.waiting = true
            } else {
                this.waiting = false
            }
        },
        async currentTasks() {
            const task = await axios.get('api/current-task/' + this.user.id)
            this.currentTask.push(...task.data)
        },
       async startWork() {
            const start = await axios.post('api/work-time', {
                task_id: this.card,
                begin : true,
                user_id : this.user.id
            })

           window.location.reload();
        }
    },
    mounted() {
        if(this.role === 'manager') {
            this.$router.push('/manager')
        }
        this.getTask()
        this.startStatus()
        this.currentTasks()
        console.log('pause:',this.waiting)
    },
    created() {
        setInterval(this.getNow, 1000);
    },
    components: {AppHeader, AppModal}
}
</script>

<style scoped>
.avatar {
    height: 200px;
}

.content_start {
    display: flex;
    justify-content: center;
}

.content_stop {
    display: flex;
    justify-content: space-between;
}

.left_block {
    height: 86vh;
    border-right: 1px solid #0a427d;
}

.btn {
    font-family: "Roboto", sans-serif;
    text-transform: uppercase;
    outline: 0;
    background: #376DA6;
    width: 100%;
    border: 0;
    padding: 8px;
    color: #FFFFFF;
    font-size: 14px;
    cursor: pointer;
    border-radius: 5px;
}

.btn_stop, .btn_pause, .btn_start {
    padding: 4px;
    width: 140px;
    height: 70px;
}

.btn:disabled {
    cursor: not-allowed;
    opacity: 1!important;
    background: #b7b881 !important;
    border-color: #ddd!important;
    /*color: #999!important;*/
}

.btn_stop:focus, .btn_pause:focus {
    box-shadow:none;
}

.btn_stop {
    background :#A63737;
    margin-right:4px;
}

.btn_pause {
    background :#D0D321;
}

.btn_add {
    border: none;
    background: inherit;
}

</style>
