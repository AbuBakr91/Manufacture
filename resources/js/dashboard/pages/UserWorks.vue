<template>
    <div class="main">
        <h4 class="text-center">Статус работ</h4>
        <table class="table table-striped mt-3">
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
                <tr v-for="item in operation[0]">
                    <td>{{item.firstname}} {{item.lastname}}</td>
                    <td>{{item.name}}</td>
                    <td>{{item.count}}</td>
                    <td>{{item.defects}}</td>
                    <td><button class="btn btn-primary">провести</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            operation: []
        }
    },
    methods: {
        async getOperation() {
            const data = await axios.get('/api/operation/')
            this.operation.push(data.data)
            console.log(...data.data)
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
