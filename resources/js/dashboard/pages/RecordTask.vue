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
            orderDetails: [],
        }
    },
    methods: {
        async getTaskJournal() {
            const dataRecord = await axios.get('/api/journal/')
             this.orderDetails.push(...dataRecord.data)
            console.log(this.orderDetails)
        }
    },
    mounted() {
        this.getTaskJournal()
    },
    components: {ShowTask}
}
</script>

<style scoped>

</style>
