<template>
    <div class="ml-3 mt-3">
        <h4 class="text-center">Журнал выполненных работ</h4>

        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Отдел</th>
                <th>Сотрудник</th>
                <th>Тех карта</th>
                <th>Время(пауза, ожидание)</th>
                <th>Количество</th>
                <th>Дата</th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="orderDetail in orderDetails">
                    <td>{{orderDetail.department}}</td>
                    <td>{{orderDetail.usersDetail[0].lastname}}</td>
                    <td>{{orderDetail.card}}</td>
                    <td>
                        {{printWorkTime(orderDetail.usersDetail[0].worktime)}}
                        <span v-if="orderDetail.usersDetail[0].paused">
                            ({{printWorkTime(orderDetail.usersDetail[0].paused)}},
                        {{printWorkTime(orderDetail.usersDetail[0].waiting)}} )
                        </span>
                    </td>
                    <td>{{orderDetail.counts}}</td>
                    <td>{{orderDetail.date}}</td>
                </tr>
<!--            <show-task v-for="orderDetail in orderDetails" :orderDetail="orderDetail"></show-task>-->
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
        },
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
        this.getTaskJournal()
        console.log(this.orderDetails)
    },
    components: {ShowTask}
}
</script>

<style scoped>

</style>
