<template>
    <div class="container">
      <h4 class="mt-3 text-center">Сотрудники</h4>
        <table class="table table-striped mt-3">
            <thead>
            <tr>
                <th style="width:22%;">Имя</th>
                <th style="width:22%">Фамилия</th>
                <th class="d-none d-md-table-cell" style="width:22%">Должность</th>
                <th class="d-none d-md-table-cell" style="width:22%">Email</th>
                <th>#</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="user in users[0]" :key="user.id">
                <td>{{user.firstname}}</td>
                <td>{{user.lastname}}</td>
                <td>{{user.slug}}</td>
                <td class="d-none d-md-table-cell">{{user.email}}</td>
                <td class="table-action">
                    <a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 align-middle"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                    <a @click.prevent="removeUser(user.id)" href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash align-middle"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>
                </td>
            </tr>
            </tbody>
        </table>
      <div class="add_user float-right">
        <button class="btn btn-primary" @click="modal = true">Добавить</button>
      </div>
      <user-modal v-if="modal" @user="addUserList" @close="modal = false"></user-modal>
    </div>
</template>

<script>
import axios from "axios";
import UserModal from "../../components/UserModal";

export default {
    data() {
        return {
            users: [],
            modal: false
        }
    },
    mounted() {
        this.getUsers()
    },
    methods: {
      async  getUsers() {
          const data =  await axios.get('/api/users')
          this.users.push(data.data)
        },
        addUserList(data) {
            this.users[0].push(data)
            console.log(this.users)
            this.modal = false
        },
       async removeUser(id) {
        const url = `http://127.0.0.1:8000/api/users/${id}`;
        await axios.delete(url)
        this.users[0] = this.users[0].filter(user => user.id !== id)
       }
    },
  components: {UserModal}
}
</script>

<style scoped>

</style>
