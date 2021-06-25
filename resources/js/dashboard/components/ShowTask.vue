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
        getNoun(number, one, two, five) {
            let n = Math.abs(number);
            n %= 100;
            if (n >= 5 && n <= 20) {
                return five;
            }
            n %= 10;
            if (n === 1) {
                return one;
            }
            if (n >= 2 && n <= 4) {
                return two;
            }
            return five;
        },
        printWorkTime(minutes) {
            if (minutes > 60) {
                const hour = Math.floor(minutes / 60)
                const mnt = Math.floor(minutes % 60)

                if (hour && mnt) {
                    return hour + ' ' + this.getNoun(hour, 'час', 'часа', 'часов') + ' : '
                        + mnt + ' ' + this.getNoun(mnt, 'минута', 'минуты', 'минут')
                }

                if (hour && !mnt) {
                    return hour + ' ' + this.getNoun(hour, 'час', 'часа', 'часов')
                }
            }

            if (minutes === null) {
                return '0 минут'
            }

            return minutes + ' ' + this.getNoun(minutes, 'минута', 'минуты', 'минут')
        }
    },
    mounted() {
        console.log(this.orderDetail)
    }
}
</script>

<style scoped>

</style>
