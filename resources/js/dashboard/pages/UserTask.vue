<template>
    <div class="container">
        <h4 class="mt-3 text-center">ТЕХ ЗАДАНИЕ</h4>
        <add-task :categories="category" v-for="department in departments" :tasks="arrayTask" :departments="department"></add-task>
    </div>
</template>

<script>
import AddTask from "../components/AddTask";
import AppAlert from "../../components/AppAlert";
import axios from "axios";
export default {
    data() {
        return {
            alert: null,
            users: [],
            departments: [],
            arrayTask: [],
            category: [],
            isSelect: false
        }
    },
    mounted() {
        this.getCategory()
        this.getDepartments()
        this.getTask()
    },
    methods: {
        async getTask() {
            const tasks = await axios.get('/api/manager-task')
            this.arrayTask.push(...tasks.data)
        },
        async  getUsers() {
            const data =  await axios.get('/api/users')
            this.users.push(...data.data)
        },
        async  getDepartments() {
            const data =  await axios.get('/api/departments')
            this.departments.push(...data.data)
        },
        async  getCategory() {
            const cat = await axios.get('/api/categories');
            this.category.push(...cat.data)
        }
    },
    components: {AddTask}
}
</script>

<style scoped>

</style>
