<template>
    <app-alert v-if="alert" :alert="alert" @close="alert = null"></app-alert>
    <div class="container">
      <h4 class="mt-3 text-center">СОТРУДНИКИ</h4>
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
            <tr v-for="user in users" :key="user.id">
                <td>{{user.firstname}}</td>
                <td>{{user.lastname}}</td>
                <td>{{user.slug}}</td>
                <td class="d-none d-md-table-cell">{{user.email}}</td>
                <td class="table-action">
                    <a @click.prevent="editUser(user.id)" href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 align-middle"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                    <a @click.prevent="removeUser(user.id)" href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash align-middle"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>
                </td>
            </tr>
            </tbody>
        </table>
      <div class="add_user float-right">
        <button class="btn btn-primary" @click="modal = true">Добавить</button>
      </div>
        <teleport to="body">
            <user-edit v-if="edit" @close="edit=false"></user-edit>
            <user-modal v-if="modal" @user="addUserList" @close="modal=false"></user-modal>
        </teleport>
    </div>
    <div class="container">
        <h4 class="mt-3 text-center">ТЕХ ЗАДАНИЕ</h4>
        <add-task :categories="category" v-for="department in departments" :departments="department"></add-task>
    </div>
</template>

<script>
import axios from "axios";
import UserModal from "../components/UserModal";
import AppAlert from "../../components/AppAlert";
import UserEdit from "../components/UserEdit";
import AddTask from "../components/AddTask";

export default {
    data() {
        return {
            users: [],
            departments: [],
            modal: false,
            edit: false,
            alert: null,
            isSelect: false,
            category: [],
            technical_card: [
                {id: 1, name: 'LV-1-hand'},
                {id: 2, name: 'Smart-Air-hand'}
            ],
        }
    },
    mounted() {
        this.getUsers()
        this.getCategory()
        this.getDepartments()
    },
    methods: {
      async  getUsers() {
          const data =  await axios.get('/api/users')
          this.users.push(...data.data)
        },
        async  getDepartments() {
            const data =  await axios.get('/api/departments')
            this.departments.push(...data.data)
        },
       async removeUser(id) {
           try {
               const person = this.users.find(user => user.id === id)
               await axios.delete('/api/users/' + id)
               this.users = this.users.filter(user => user.id !== id)

               this.alert = {
                   type: 'primary',
                   title: 'Успешно!',
                   text: `Пользователь с именем "${person.firstname} ${person.lastname}" успешно удален!`
               }
           } catch (e) {
               this.alert = {
                   type: 'danger',
                   title: 'Ошбика!',
                   text: e.message
               }
           }
       },
        addUserList(data) {
            this.users.push(data)
            this.modal = false
            this.alert = {
                type: 'primary',
                title: 'Успешно!',
                text: `Пользователь с именем "${data.firstname} ${data.lastname}" успешно добавлен!`
            }
        },
      async  getCategory() {
          const cat = await axios.get('/api/categories');
          this.category.push(...cat.data)
        }
    },
  components: {AppAlert, UserModal, UserEdit, AddTask}
}
</script>

<style scoped>

</style>
