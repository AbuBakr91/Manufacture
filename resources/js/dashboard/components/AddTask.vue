<template>
    <div class="user__info mt-3 ml-3">
        <a href="#" @click.prevent="isSelect = !isSelect" class="user__click">{{departments.name}}</a>
        <div class="technical_task d-flex" v-if="arrayTask.length !== 0">
            <ul class="list-group list-group-numbered mb-2">
                <li class="list-group-item" v-for="task in arrayTask">
                    <span>Тех. карта:</span> {{ task.name }} - <span>Количество:</span><b>{{ task.count }}</b>
                    <i @click="removeTask(task.id)" class="bi bi-x-square-fill"></i>
                </li>
            </ul>
        </div>
        <div class="selects" v-if="isSelect">
            <select class="form-control category" @change="getCards(category)" v-model="category" name="category" id="category">
                <option value="0">Выберите категорию</option>
                <option v-for="cat in categories" :value="cat.id">{{cat.name}}</option>
            </select>
            <select class="form-control category" v-model="card_id" name="category" id="technical_card">
                <option value="0">Выберите техкарту</option>
                <option v-for="card in cards" :value="card.id">{{card.name}}</option>
            </select>
            <input type="number" class="form-control category" v-model="count" placeholder="Введите количество">
            <button class="btn" @click="addTask">Выставить</button>
        </div>
    </div>
</template>

<script>
export default {
    props: ['departments', 'categories'],
    data() {
        return {
            isSelect: false,
            category: '0',
            cards: [],
            card_id: '0',
            dep_id: this.departments.id,
            count: '',
            arrayTask: []
        }
    },
    mounted() {
        this.getTask()
        //слушатель для sockets
        // window.Echo.channel('show-task').listen('ShowTask', ({tas}) => {
        //     this.arrayTask.push(tas)
        //     console.log(tas)
        // })
    },
    methods: {
        //вернуть задания для отдела
        async getTask() {
          const tasks = await axios.get('/api/manager-task/' + this.dep_id)
            this.arrayTask.push(...tasks.data)
        },
        //выставить новое задание для отдела
        async addTask() {
            await axios.post('/api/manager-task', {
                dep_id: this.dep_id,
                card_id: this.card_id,
                counts: this.count,
            })
            this.arrayTask.push({
                name: this.card_id, counts: this.count
            })
            this.isSelect = !this.isSelect
            window.location.reload();
        },
        //удалить задание отдела
        async removeTask(id) {
            await axios.delete('/api/manager-task/' + id)
            this.arrayTask = this.arrayTask.filter(task => task.id !== id)
        },
        //взять карты по категории
        async getCards(id)
        {
            if (this.cards.length === 0) {
                const card = await axios.get('/api/cards/' + id)
                this.cards.push(...card.data)
            } else {
                this.cards.length = 0
                const card = await axios.get('/api/cards/' + id)
                this.cards.push(...card.data)
            }
        }
    }
}
</script>

<style scoped>
span {
    font-weight: bold;
    font-size: 16px;
}

.bi {
    padding: 8px;
    cursor: pointer;
    color: red;
}

.bi::before {
    font-size: 20px;
}

.task_style {
    font-size: 18px;
    padding: 8px;
}

.category {
    width: 250px;
    margin-right: 16px;
}

.user__click {
    color: #000;
    font-size: 20px;
    cursor: pointer;
    font-weight: bold;
}

.user__click:hover {
    color: #cd2a01;
    text-decoration: none;
}

.btn {
    padding: 8px;
    background: #376DA6;
    color: #fff;
    height: 37px;
}

.selects {
    display: flex;
}
</style>
