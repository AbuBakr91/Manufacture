<template>
    <tr>
        <td>Сборка</td>
        <td>SN-2-flash</td>
        <td>90</td>
        <td>21/06/2021</td>
        <td><button class="btn btn-primary" @click="show = !show">Подробнее</button></td>
    </tr>
    <tr v-if="show">
        <td><b>Сотрудник:</b></td>
        <td><b>Количество:</b></td>
        <td><b>Пауза:</b></td>
        <td><b>Ожидание:</b></td>
    </tr>
    <tr v-if="show">
        <td>Устинов</td>
        <td>90 шт.</td>
        <td>52 минуты</td>
        <td>0</td>
    </tr>
<!--    <div v-if="show">-->
<!--        <div class="row">-->
<!--            <div class="col-6">-->
<!--                <h4>Пауза: {{task.paused}}</h4>-->
<!--            </div>-->
<!--            <div class="col-6">-->
<!--                <h4>Ожидание: {{task.waiting}}</h4>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
</template>

<script>
export default {
    props : ['task'],
    data() {
        return {
            show: false
        }
    },
    methods: {
    }
}
</script>

<style scoped>

</style>
