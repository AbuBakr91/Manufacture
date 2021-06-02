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
                    <div class="row mt-5">
                        <div class="col-2 text-center">
                            <h1>{{dataTime}}</h1>
                        </div>
                        <div class="col-8">
                            <select class="form-control form-control-lg">
                                <option>Product 1</option>
                                <option>Product 2</option>
                            </select>
<!--                            <small class="float-right"><button class="btn_add">+добавить</button></small>-->
                        </div>
                        <div class="col-2" v-if="!start">
                            <button class="btn primary" @click="start = !start" type="submit">START</button>
                        </div>
                        <div class="col-2 d-flex" v-if="start">
                            <button class="btn btn_stop" @click="stopFuture" type="submit">STOP</button>
                            <button class="btn btn_pause" @click="pause = !pause" v-html="pause ? pauseIcon : 'PAUSE'" type="submit"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <app-modal v-if="modal" @close="modal = false"></app-modal>
</template>

<script>
import store from "../store"
import AppHeader from "../Components/AppHeader";
import AppModal from "../components/AppModal";
export default {
    data() {
        return {
            modal:false,
            start: false,
            pause: false,
            dataTime: '',
            user: JSON.parse(store.state.auth.user),
            role: store.state.auth.role,
            pauseIcon: "<svg fill='#fff' height='20px' id='Layer_1' style='enable-background:new 0 0 512 512;' version='1.1' viewBox='0 0 512 512' width='20px' xml:space='preserve' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'><g><path d='M224,435.8V76.1c0-6.7-5.4-12.1-12.2-12.1h-71.6c-6.8,0-12.2,5.4-12.2,12.1v359.7c0,6.7,5.4,12.2,12.2,12.2h71.6   C218.6,448,224,442.6,224,435.8z'/><path d='M371.8,64h-71.6c-6.7,0-12.2,5.4-12.2,12.1v359.7c0,6.7,5.4,12.2,12.2,12.2h71.6c6.7,0,12.2-5.4,12.2-12.2V76.1   C384,69.4,378.6,64,371.8,64z'/></g></svg>"
        }
    },
    methods: {
        getNow() {
            this.dataTime = new Date().toLocaleTimeString()
        },
        stopFuture() {
            this.start = !this.start
            this.modal = !this.modal
        },
        printRole(slug) {
          if (slug === 'collector') {
            return 'Сборщик'
          } else if(slug === 'machine-operator') {
            return 'Оператор станка'
          }
            return 'Пайщик'
        }
    },
    mounted() {
        if(this.role === 'manager') {
            this.$router.push('/manager')
        }
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

.btn_stop, .btn_pause {
    padding: 4px;
    width: 60px;
    height: 38px;
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
