    <template>
    <div class="ml-3 mt-3">
        <h4 class="text-center">Журнал выполненных работ</h4>
        <div class="search__block mb-2">
            <Calendar v-model="value" @change="searchDate()" :showButtonBar="true" dateFormat="yy-mm-dd" selectionMode="range" placeholder="поиск по дате..."/>
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
            value: '',
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
        searchDate(e) {
            console.log(this.value);
            this.$nextTick(() => console.log(this.value));
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
        console.log(this.orderDetails)
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
