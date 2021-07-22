<template>
    <div class="container d-flex justify-content-center" v-for="value in user">
        <div class="col-8">
            <h4 class="mt-3">Введите данные для редактирования</h4>
            <form @submit.prevent>
                <div class="form-group">
                    <label for="firstname" class="col-form-label">Имя</label>
                    <input type="text" v-model="value.firstname" class="form-control" id="firstname">
                    <label for="lastname" class="col-form-label">Фамилия</label>
                    <input type="text" v-model="value.lastname" class="form-control" id="lastname">
                </div>
                <div class="form-group">
                    <label for="select-slug" class="col-form-label">Отдел</label>
                    <select class="form-control" name="select-slug" id="select-slug" v-model="value.slug">
                        <option v-for="item in position" :value="item.id">{{item.name}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="select-office" class="col-form-label">Офис</label>
                    <select class="form-control" name="select-office" id="select-office" v-model="value.office">
                        <option v-for="item in office" :value="item.id">{{item.name}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <div class="form-group">
                        <label for="email" class="col-form-label">Email</label>
                        <input type="email" v-model="value.email"  class="form-control" id="email">
                        <label for="password" class="col-form-label">Password</label>
                        <input type="password" v-model="password" class="form-control" id="password">
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="$emit('close')" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" @click="addUser">Сохранить</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            id: this.$route.params.id,
            position: [],
            office: [],
            user: [],
            alert: null,
        }
    },
    methods: {
        async getUser() {
            const userData = await axios.get('/api/users/'+ this.id)
            this.user.push(...userData.data)
        },
        async getPosition() {
            const data = await axios.get('/api/departments')
            this.position.push(...data.data)
        },
        async getWorkRoom() {
            const data = await axios.get('/api/work-rooms')
            this.office.push(...data.data)
        },
    },

    mounted() {
        this.getUser()
        this.getPosition()
        this.getWorkRoom()
        console.log(this.user)
    }
}
</script>

<style scoped>

</style>
