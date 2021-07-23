    <template>
    <div class="ml-3 mt-3">
        <h4 class="text-center">Журнал выполненных работ</h4>
        <div class="search__block mb-2">
            <Calendar v-model="value" @date-select="searchDate" @clear-click="clear" :showButtonBar="true" dateFormat="yy-mm-dd" placeholder="поиск по дате..."/>
            <input type="search" class="form-control search" placeholder="поиск..." v-model="search">
        </div>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Отдел</th>
                <th>Сотрудник</th>
                <th>Тех карта</th>
                <th>Время(пауза, ожидание)</th>
                <th>Количество</th>
                <th>Брак</th>
                <th>Дата</th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="data in result">
                    <td>{{data.department}}</td>
                    <td>{{data.lastname}}</td>
                    <td>{{data.name}}</td>
                    <td>
                        {{printWorkTime(data.worktime, data.paused, data.waiting)}}
                        <span v-if="data.paused">
                            ({{printWorkTime(data.paused)}}, {{ printWorkTime(data.waiting)}})
                        </span>
                    </td>
                    <td>{{data.count}}</td>
                    <td>{{data.defects}}</td>
                    <td>{{data.finish}}</td>
                </tr>
            </tbody>
            <div class="mt-2" v-if="countRows">
                <h4>Результат поиска {{searchWords(countRows)}}</h4>
            </div>
        </table>
    </div>
</template>

<script>
import Fuse from 'fuse.js'
import Calendar from 'primevue/calendar';
export default {
    data() {
        return {
            tasks: [],
            orderDetails: [],
            search: '',
            fuse: null,
            result: [],
            countRows: '',
            value: null,
            time: null,
            options : {
                shouldSort: true,
                tokenize: true,
                matchAllTokens: true,
                threshold: 0,
                location: 0,
                distance: 0,
                isCaseSensitive: false,
                findAllMatches: true,
                includeScore: true,
                keys: [
                    "department",
                    "name",
                    "lastname",
                    "finish"
                ]
            }
        }
    },
    methods: {
        async getTaskJournal() {
            const dataRecord = await axios.get('/api/journal/')
             this.orderDetails.push(...dataRecord.data)
        },
        clear() {
            // document.querySelector('.p-inputtext').value = ''
            this.value = null
            // console.log(document.getElementsByClassName('p-inputtext').value)
            console.log(this.value)
        },
        formatDate(date) {
            // if(date[1] === null) {
                let d = new Date(date),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();

                if (month.length < 2)
                    month = '0' + month;
                if (day.length < 2)
                    day = '0' + day;

                return [year, month, day].join('-');
            // } else {
            //     let d = new Date(date[0]),
            //         month = '' + (d.getMonth() + 1),
            //         day = '' + d.getDate(),
            //         year = d.getFullYear();
            //
            //     if (month.length < 2)
            //         month = '0' + month;
            //     if (day.length < 2)
            //         day = '0' + day;
            //
            //     let d2 = new Date(date[1]),
            //         month2 = '' + (d2.getMonth() + 1),
            //         day2 = '' + d2.getDate(),
            //         year2 = d2.getFullYear();
            //
            //     if (month2.length < 2)
            //         month2 = month;
            //     if (day2.length < 2)
            //         day2 = '0' + day2;
            //
            //     return [year, month, day].join('-') + ' ' + [year2, month2, day2].join('-');
            // }

        },
        searchDate() {
            console.log(this.formatDate(this.value));
        },
        searchWords(count) {
            return count + ' ' + this.getNoun(count, 'строка', 'строки', 'строк')
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
        printWorkTime(time, paused = 0, waiting = 0) {
            let minutes = time - paused - waiting

            if (minutes > 60) {
                const hour = Math.floor(minutes / 60)
                const mnt = Math.floor(minutes % 60)

                if (hour && mnt) {
                    return hour + ' : '
                        + mnt + ' ' + this.getNoun(mnt, 'минута', 'минуты', 'минут')
                }

                if (hour && !mnt) {
                    return hour + ' ' + this.getNoun(hour, 'час', 'часа', 'часов')
                }
            }

            if (minutes == null || minutes < 0) {
                return '0 минут'
            }

            return minutes + ' ' + this.getNoun(minutes, 'минута', 'минуты', 'минут')
        }
    },
    mounted() {
        this.getTaskJournal()
        this.result = this.orderDetails
        console.log(this.value)
    },
    watch: {
        search() {
            this.fuse = new Fuse(this.orderDetails, this.options);
            if (this.search.trim() === '') {
                this.result = this.orderDetails
                this.countRows = ''
            } else {
                this.result = this.fuse.search(this.search.trim()).map(result => result.item)
                this.countRows = this.result.length
            }
        },
        value() {
            this.fuse = new Fuse(this.orderDetails, this.options);
            if (this.value == null) {
                this.result = this.orderDetails
                this.countRows = ''
            } else {
                this.result = this.fuse.search(this.formatDate(this.value)).map(result => result.item)
                console.log(this.fuse.search(this.formatDate(this.value)))
                this.countRows = this.result.length
            }
        }
    },
    components: {Calendar}
}
</script>

<style scoped>
.search {
    width: 300px;
    float: right;
}
.p-calendar {
    width: 300px;
    height: 37px;
}
</style>
