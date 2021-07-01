<template>
    <div class="main">
        <h4 class="text-center">Статус работ</h4>
        <table class="table table-striped mt-3" v-if="operation.length">
            <thead>
            <tr>
                <th style="width:22%;">Сотрудник</th>
                <th style="width:22%;">Тех. карта</th>
                <th style="width:22%">Количество</th>
                <th class="d-none d-md-table-cell" style="width:22%">Брак</th>
                <th class="d-none d-md-table-cell" style="width:22%">Статус</th>
            </tr>
            </thead>
            <tbody>
                <operation-work @success="removeTask" :task="item" v-for="item in operation"></operation-work>
            </tbody>
        </table>
        <h4 class="text-center mt-3" v-else>Нет операций для проведения</h4>
    </div>
</template>

<script>
import OperationWork from "../components/OperationWork";
export default {
    components: {OperationWork},
    data() {
        return {
            operation: []
        }
    },
    methods: {
        async getOperation() {
            const data = await axios.get('/api/operation/')
            this.operation.push(...data.data)
        },
        removeTask(id) {
            this.operation = this.operation.filter(item => item.tech_id !== id)
            console.log(id)
            console.log(this.operation)
        }
    },
    mounted() {
        this.getOperation()
        console.log(this.operation)
    }
}
</script>

<style scoped>
.main {
    margin: 20px 15px;
}
</style>
