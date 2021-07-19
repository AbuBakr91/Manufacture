<template>
    <tr>
        <td>{{orderDetail.department}}</td>
        <td>{{orderDetail.card}}</td>
        <td>{{orderDetail.counts}}</td>
        <td>{{orderDetail.date}}</td>
        <td><button class="btn btn-primary" @click="show = !show">Подробнее</button></td>
    </tr>
    <tr v-if="show">
        <td><b>Сотрудник:</b></td>
        <td><b>Количество:</b></td>
        <td><b>Время:</b></td>
        <td><b>Пауза:</b></td>
        <td><b>Ожидание:</b></td>
    </tr>
    <tr v-if="show" v-for="user in orderDetail.usersDetail">
        <td>{{user.lastname}}</td>
        <td>{{user.count}} шт.</td>
        <td>{{printWorkTime(user.worktime) }}</td>
        <td>{{printWorkTime(user.paused)}}</td>
        <td>{{printWorkTime(user.waiting)}}</td>
    </tr>
</template>

<script>
export default {
    props : ['orderDetail'],
    data() {
        return {
            show: false,
        }
    },
    methods: {
        
    },
    mounted() {
        console.log(this.orderDetail)
    }
}
</script>

<style scoped>

</style>
