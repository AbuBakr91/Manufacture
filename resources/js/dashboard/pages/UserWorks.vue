<template>
    <div class="main">
        <app-alert v-if="alert" :alert="alert" @close="alert = null"></app-alert>
        <h4 class="text-center">Статус работ</h4>
        <table class="table table-striped mt-3" v-if="operation.length">
            <thead>
            <tr>
                <th>Сотрудник</th>
                <th>Тех. карта</th>
                <th>Количество</th>
                <th>Брак</th>
                <th>Время</th>
                <th class="d-none d-md-table-cell">Статус</th>
            </tr>
            </thead>
            <tbody>
                <operation-work @danger="infoCode" @success="removeTask" :task="item" v-for="item in operation"></operation-work>
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
        },
        infoCode(data) {
            this.alert = {
                type: 'danger',
                title: `Ошибка: ${data.code}`,
                text: data.message
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
