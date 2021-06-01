<template>
    <div class="container">
        <table class="table table-striped">
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
                    <a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash align-middle"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import axios from "axios";
export default {
    data() {
        return {
            users: []
        }
    },
    mounted() {
        this.getUsers()
    },
    methods: {
      async  getUsers() {
          const data =  await axios.get('/api/users')
          this.users.push(data.data)
          console.log(this.users)
        }
    }
}
</script>

<style scoped>

</style>
