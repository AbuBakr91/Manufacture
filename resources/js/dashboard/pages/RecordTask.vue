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
            <show-task v-for="orderDetail in orderDetails" :orderDetail="orderDetail"></show-task>
            </tbody>
        </table>
    </div>
</template>

<script>
import ShowTask from "../components/ShowTask"

export default {
    data() {
        return {
            tasks: [],
            orderDetails: [
                { department: "Сборка", card: "SN-2-flash", counts: 90, date: "21/06/2021", usersDetail:
                        [
                            {name: "Устинов", count: 10, worktime: 20, paused: 23, waiting: 0},
                            {name: "Лагизов", count: 44, worktime: 345, paused: 33, waiting: 0}
                        ]
                },
                { department: "Сборка", card: "SN-2-flash", counts: 90, date: "21/06/2021", usersDetail:
                        [
                            {name: "Устинов", count: 50, worktime: 163, paused: 53, waiting: 0},
                            {name: "Лагизов", count: 40, worktime: 143, paused: 35, waiting: 0}
                        ]
                }
            ],
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
