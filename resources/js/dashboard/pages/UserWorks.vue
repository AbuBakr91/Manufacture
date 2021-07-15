<template>
    <div class="main">
        <app-alert v-if="alert" :alert="alert" @close="alert = null"></app-alert>
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
import AppAlert from "../../components/AppAlert";
export default {
    data() {
        return {
            operation: [],
            alert: null
        }
    },
    methods: {
        async getOperation() {
            const data = await axios.get('/api/operation/')
            this.operation.push(...data.data)
        },
        removeTask(id) {
            this.operation = this.operation.filter(item => item.tech_id !== id)
            this.alert = {
                type: 'primary',
                title: 'Успешно!',
                text: `Операция успешно проведена!`
            }
        }
    },
    mounted() {
        this.getOperation()
    },
    components: {OperationWork, AppAlert}
}
</script>

<style scoped>
.main {
    margin: 20px 15px;
}
</style>
