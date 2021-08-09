    <template>
    <div class="ml-3 mt-3">
        <h4 class="text-center">Журнал выполненных работ</h4>
        <div class="search__block mb-2 mt-3">
            <Calendar v-model="value" @clear-click="clear" selectionMode="range" :showButtonBar="true" dateFormat="yy-mm-dd" placeholder="поиск по дате..."/>
            <select class="form-control w-25" v-model="searchUser">
                <option value="0">Выберите сотрудника</option>
                <option v-for="user in users" :value="user.lastname">{{user.lastname}}</option>
            </select>
            <input type="search" class="form-control search" placeholder="поиск..." v-model="search">
        </div>
        <div class="table-scroll">
            <table class="table table-striped main-table table-hover">
            <thead>
            <tr>
                <th @click="sortTable('department')">Отдел
                    <div class="arrow" v-if="'department' === sortColumn" :class="ascending ? 'arrow_up' : 'arrow_down'"></div>
                </th>
                <th @click="sortTable('lastname')">Сотрудник
                    <div class="arrow" v-if="'lastname' === sortColumn" :class="ascending ? 'arrow_up' : 'arrow_down'"></div>
                </th>
                <th @click="sortTable('name')">Тех карта
                    <div class="arrow" v-if="'name' === sortColumn" :class="ascending ? 'arrow_up' : 'arrow_down'"></div>
                </th>
                <th @click="sortTable('worktime')">Время(пауза, ожидание)
                    <div class="arrow" v-if="'worktime' === sortColumn" :class="ascending ? 'arrow_up' : 'arrow_down'"></div>
                </th>
                <th @click="sortTable('count')">Количество
                    <div class="arrow" v-if="'count' === sortColumn" :class="ascending ? 'arrow_up' : 'arrow_down'"></div>
                </th>
                <th @click="sortTable('defects')">Брак
                    <div class="arrow" v-if="'defects' === sortColumn" :class="ascending ? 'arrow_up' : 'arrow_down'"></div>
                </th>
                <th @click="sortTable('finish')">Дата
                    <div class="arrow" v-if="'finish' === sortColumn" :class="ascending ? 'arrow_up' : 'arrow_down'"></div>
                </th>
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
            <tr class="mt-2 search__result">
                <td>Итого:</td>
                <td></td>
                <td></td>
                <td>{{allTime}}
                    <span v-if="allPaused">
                            ({{printWorkTime(allPaused)}}, {{ printWorkTime(allWaiting)}})
                    </span>
                </td>
                <td>{{allCount}}</td>
                <td>{{allDefects}}</td>
                <td></td>
            </tr>
        </table>
        </div>
    </div>
</template>

<script>
import Fuse from 'fuse.js'
import Calendar from 'primevue/calendar';
import Moment from 'moment';
import { extendMoment } from 'moment-range';
import { mapGetters } from 'vuex'

export default {
    data() {
        return {
            tasks: [],
            ascending: false,
            sortColumn: '',
            orderDetails: [],
            searchUser: 0,
            search: '',
            allTime: 0,
            allCount: 0,
            allDefects: 0,
            allPaused: 0,
            allWaiting: 0,
            users: [],
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
        async getUsers() {
            const users = await axios.get('/api/users')
            this.users.push(...users.data)
        },
        clear() {
            this.value = ''
            this.value = null
        },
        sortTable(col) {
            if (this.sortColumn === col) {
                this.ascending = !this.ascending;
            } else {
                this.ascending = true;
                this.sortColumn = col;
            }

            let ascending = this.ascending;

            this.result.sort(function(a, b) {
                if (a[col] > b[col]) {
                    return ascending ? 1 : -1
                } else if (a[col] < b[col]) {
                    return ascending ? -1 : 1
                }
                return 0;
            })
        },
        formatDate(date) {
            if(date[1] === null) {
                let d = new Date(date),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();

                if (month.length < 2)
                    month = '0' + month;
                if (day.length < 2)
                    day = '0' + day;

                return {firstDate: [year, month, day].join('-')}
            } else {
                let d = new Date(date[0]),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();

                if (month.length < 2)
                    month = '0' + month;
                if (day.length < 2)
                    day = '0' + day;

                let d2 = new Date(date[1]),
                    month2 = '' + (d2.getMonth() + 1),
                    day2 = '' + d2.getDate(),
                    year2 = d2.getFullYear();

                if (month2.length < 2)
                    month2 = month;
                if (day2.length < 2)
                    day2 = '0' + day2;

                return {firstDate: [year, month, day].join('-'), secondDate: [year2, month2, day2].join('-')}
            }

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
        this.getUsers()
        this.result = this.orderDetails
    },
    computed: {
        allWaiting() {
            this.allWaiting = this.result.reduce((total, item) => {
                return total + item.waiting
            }, 0)

            return this.allWaiting
        },
        allPaused() {
            this.allPaused = this.result.reduce((total, item) => {
                return total + item.paused
            }, 0)

            return this.allPaused
        },
        allCount() {
            this.allCount = this.result.reduce((total, item) => {
                return total + item.count
            }, 0)

            return this.allCount
        },
        allTime() {
            this.allTime = this.result.reduce((total, item) => {
                total += item.worktime - item.paused - item.waiting
                if (total < 0 ) {
                    return 0
                }
                return total
            }, 0)

            return this.printWorkTime(this.allTime)
        },
        allDefects() {
            this.allDefects = this.result.reduce((total, item) => {
                return total + item.defects
            }, 0)

            return this.allDefects
        }

    },
    watch: {
        search() {
            if (this.result.length === this.orderDetails.length) {
                this.fuse = new Fuse(this.orderDetails, this.options);
                if (this.search.trim() === '') {
                    this.result = this.orderDetails
                    this.countRows = ''
                } else {
                    this.result = this.fuse.search(this.search.trim()).map(result => result.item)
                    this.countRows = this.result.length

                    this.allCount = this.result.reduce((total, item) => {
                        return total + item.count
                    }, 0)

                    this.allTime = this.result.reduce((total, item) => {
                        return total + item.worktime
                    }, 0)

                    this.AllDefects = this.result.reduce((total, item) => {
                        return total + item.defects
                    }, 0)
                }
            } else {
                this.fuse = new Fuse(this.result, this.options);
                if (this.search.trim() === '') {
                    this.result = this.orderDetails
                    this.countRows = ''
                } else {
                    this.result = this.fuse.search(this.search.trim()).map(result => result.item)
                    this.countRows = this.result.length

                    this.allCount = this.result.reduce((total, item) => {
                        return total + item.count
                    }, 0)

                    this.allTime = this.result.reduce((total, item) => {
                        return total + item.worktime
                    }, 0)

                    this.AllDefects = this.result.reduce((total, item) => {
                        return total + item.defects
                    }, 0)
                }
            }
        },
        searchUser() {
            if (this.result.length === this.orderDetails.length) {
                this.fuse = new Fuse(this.orderDetails, this.options);
                if (this.searchUser === '0') {
                    this.result = this.orderDetails
                    this.countRows = ''
                } else {
                    this.result = this.fuse.search(this.searchUser.trim()).map(result => result.item)
                    this.countRows = this.result.length

                    this.allCount = this.result.reduce((total, item) => {
                        return total + item.count
                    }, 0)

                    this.allTime = this.result.reduce((total, item) => {
                        total += item.worktime - item.paused - item.waiting
                        if (total < 0 ) {
                            return 0
                        }
                        return total
                    }, 0)

                    this.AllDefects = this.result.reduce((total, item) => {
                        return total + item.defects
                    }, 0)
                }
            } else {
                this.fuse = new Fuse(this.result, this.options);
                this.result = this.fuse.search(this.searchUser.trim()).map(result => result.item)
                this.countRows = this.result.length

                this.allCount = this.result.reduce((total, item) => {
                    return total + item.count
                }, 0)

                this.allTime = this.result.reduce((total, item) => {
                    total += item.worktime - item.paused - item.waiting
                    if (total < 0 ) {
                        return 0
                    }
                    return total
                }, 0)

                this.AllDefects = this.result.reduce((total, item) => {
                    return total + item.defects
                }, 0)
            }
        },
       async value() {
            this.fuse = new Fuse(this.orderDetails, this.options);
            if (this.value == null) {
                this.result = this.orderDetails
                this.countRows = ''
            } else {
                const dateRange = this.formatDate(this.value)
                if (!dateRange.secondDate) {
                    this.result = this.fuse.search(dateRange.firstDate).map(result => result.item)
                    this.countRows = this.result.length
                } else {
                   const response = await axios.post('/api/date-range', {
                        start: dateRange.firstDate,
                        end: dateRange.secondDate
                    })
                    this.result.push(...response.data)
                    // this.$store.dispatch('getTaskDetails', response.data)
                }
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

.arrow_down {
    background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB8AAAAaCAYAAABPY4eKAAAAAXNSR0IArs4c6QAAAvlJREFUSA29Vk1PGlEUHQaiiewslpUJiyYs2yb9AyRuJGm7c0VJoFXSX9A0sSZN04ULF12YEBQDhMCuSZOm1FhTiLY2Rky0QPlQBLRUsICoIN/0PCsGyox26NC3eTNn3r3n3TvnvvsE1PkwGo3yUqkkEQqFgw2Mz7lWqwng7ztN06mxsTEv8U0Aam5u7r5EInkplUol/f391wAJCc7nEAgE9Uwmkzo4OPiJMa1Wq6cFs7Ozt0H6RqlUDmJXfPIx+qrX69Ti4mIyHA5r6Wq1egND+j+IyW6QAUoul18XiUTDNHaSyGazKcZtdgk8wqhUKh9o/OMvsVgsfHJy0iWqVrcQNRUMBnd6enqc9MjISAmRP3e73T9al3XnbWNjIw2+KY1Gc3imsNHR0YV4PP5+d3e32h3K316TySQFoX2WyWR2glzIO5fLTSD6IElLNwbqnFpbWyO/96lCoai0cZjN5kfYQAYi5H34fL6cxWIZbya9iJyAhULBHAqFVlMpfsV/fHxMeb3er+Vy+VUzeduzwWC45XA4dlD/vEXvdDrj8DvURsYEWK3WF4FA4JQP9mg0WrHZbEYmnpa0NxYgPVObm5teiLABdTQT8a6vrwdRWhOcHMzMzCiXlpb2/yV6qDttMpkeshEzRk4Wo/bfoe4X9vb2amzGl+HoXNT29vZqsVi0sK1jJScG+Xx+HGkL4Tew2TPi5zUdQQt9otPpuBk3e0TaHmMDh1zS7/f780S0zX6Yni+NnBj09fUZUfvudDrNZN+GkQbl8Xi8RLRtHzsB9Hr9nfn5+SjSeWUCXC7XPq5kw53wsNogjZNohYXL2EljstvtrAL70/mVaW8Y4OidRO1/gwgbUMvcqGmcDc9aPvD1gnTeQ+0nmaInokRj0nHh+uvIiVOtVvt2a2vLv7Ky0tL3cRTXIcpPAwMDpq6R4/JXE4vFQ5FI5CN+QTaRSFCYc8vLy1l0rge4ARe5kJ/d27kYkLXoy2Jo4C7K8CZOsEBvb+9rlUp1xNXPL7v3IDwxvPD6AAAAAElFTkSuQmCC')
}

.arrow_up {
    background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAaCAYAAACgoey0AAAAAXNSR0IArs4c6QAAAwpJREFUSA21Vt1PUmEYP4dvkQ8JFMwtBRocWAkDbiqXrUWXzU1rrTt0bdVqXbb1tbW16C9IBUSmm27cODdneoXjputa6069qwuW6IIBIdLvdaF4OAcOiGeDc87zPs/vd57P96WpFq7p6enbGo1mjKZpeTabjU1MTCRagGnOZHFxcXxtbe1XKpUq7+zslJeXl//Mz8+Hy+Uy3RxSE9qTk5M3otFooVQqgef4Wl9f343FYoEmoISrxuNxFX5f9vb2jhn/PxUKhfLS0tIPfFifUESRUMV8Pv/M6XReRm5rTGQyGeXxeGxYe1ezeBpBOBx2rKysbO7v79d4Wy3Y2Nj4GQqFbgnhaugxwiuGJx99Pp9FLBbXxYTXvTqd7v3MzIy6riIWGxJnMpl7AwMD14xGYyMsSq1WUyQdUqn0eSPlusQIsbGrq+vl4OCgvhFQZd1utyv1en0gEolcqsi47nWJlUrlG5fLZVcoFFy2nDKSDpIWlUoVTCQSEk4lCHmJMZ2GTCbTiMVikfIZ88l7enoos9l8dXt7+z6fDicxSJUokqDX6xXcl2wCROoc0vQCWL3sNfLOSdzR0fHY4XC4tVotl40gmVwup9xuN4OQv+UyqCFGH9rg7SOGYVRcBs3IEG4J0nVnamrqOtvuBDGGgQg9+wHFcVEi4a0LNkbdd6TrPKo8ODc311mteIIYjT/a398/jK+s1jnVM0kXoufCFvq0GuiIGEVgQIhfoygM1QrteEa9dAL7ITiYCt4RMabOK5AyKKzKWtvupLcRciu8D5J0EuDDPyT/Snd39yh6VtY2NhYQSR9G79Ds7OxdskRjEyAufvb7/cPoO5Z6e1+xtVKrq6vfcFzyi/A3ZrPZ3GdNSlwgo5ekE4X2RIQGf2C1WlufFE0GBeGWYQ8YERWLxQtnUVB830MKLZfL9RHir8lkssCn2G751tZWEWe03zTKm15YWPiEiXXTYDB0Ig/t7yd8PRws4EicwWHxO4jHD8/C5HiTTqd1BwcHFozKU89origB+y/kmzgYpgOBQP4fGmUiZmJ+WNgAAAAASUVORK5CYII=')
}

.arrow {
    float: right;
    width: 12px;
    height: 15px;
    background-repeat: no-repeat;
    background-size: contain;
    background-position-y: bottom;
}

.search__result {
    font-weight: bold;
}

table th, .search__result td {
    text-transform: uppercase;
    text-align: left;
    cursor: pointer;
}

table th:hover {
    background: #222e3c;
    color: #fff;
}

.search__block {
    display: flex;
    justify-content: space-around;
}

.table-scroll {
    position: relative;
    width:100%;
    z-index: 1;
    margin: auto;
    overflow: auto;
    height: 100vh;
}

.table-scroll table {
    width: 100%;
    min-width: 1080px;
    margin: auto;
    border-collapse: separate;
    border-spacing: 0;
}
.table-wrap {
    position: relative;
}
.table-scroll th,
.table-scroll td {
    background: #fff;
    vertical-align: top;
}
.table-scroll thead th {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
}
/* safari and ios need the tfoot itself to be position:sticky also */
.table-scroll tfoot,
.table-scroll tfoot th,
.table-scroll tfoot td {
    position: -webkit-sticky;
    position: sticky;
    bottom: 0;
    z-index:4;
}

th:first-child {
    position: -webkit-sticky;
    position: sticky;
    left: 0;
    z-index: 2;
}
thead th:first-child,
tfoot th:first-child {
    z-index: 5;
}
</style>
