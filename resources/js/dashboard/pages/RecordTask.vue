<template>
    <div class="ml-3 mt-3">
        <h4 class="text-center">Журнал выполненных работ</h4>

        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Отдел</th>
                <th>Тех карта</th>
                <th>Количество</th>
                <th>Дата</th>
                <th>#</th>
            </tr>
            </thead>
            <tbody>
                <show-task v-for="task in tasks" :task="task"></show-task>
            </tbody>
        </table>
    </div>
</template>

<script>
import ShowTask from "../components/ShowTask"

export default {
    data() {
        return {
            tasks: [1]
        }
    },
    methods: {
        async getTaskJournal() {
            const dataRecord = axios.get('/api/journal')

            this.tasks.push(...dataRecord.data)
        }
    },
    components: {ShowTask}
}
</script>

<style scoped>

</style>
