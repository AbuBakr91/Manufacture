<template>
    <div class="container mt-3">
        <div class="row">
            <h4 class="text-center"> Сортировка по категориям</h4>
            <select class="form-control category w-75 m-3"
                    v-model="category"
                    @change="getCards(category)"
                    name="category"
                    id="category">
                <option :value="0">Выберите категорию</option>
                <option v-for="cat in categories" :value="cat.id">{{cat.name}}</option>
            </select>
            <div class="col-10">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th style="width: 20%">Название</th>
                            <th>Среднее время</th>
                            <th>Ручной ввод</th>
                            <th style="width: 20%">Обновлено</th>
                        </tr>
                    </thead>
                    <tbody>
                        <show-task v-for="(card,i) in cards" :card="card" :i="i"></show-task>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import ShowTask from "../components/ShowTask";
export default {
    data() {
        return {
            data: null,
            cards: [],
            category: 0,
            categories: []
        }
    },
    mounted(){
        this.getAllCards()
        this.getCategory()
    },
    methods: {
        async  getAllCards() {
          const cards = await axios.get('/api/tech_card')
          this.cards.push(...cards.data)
        },
        async getCards(id) {
            if (id === 0) {
                const cards = await axios.get('/api/tech_card')
                this.cards.push(...cards.data)
                console.log(this.cards)
            } else {
                const cards = await axios.get('/api/tech_card')
                this.cards.push(...cards.data)
                this.cards = this.cards.filter(card => card.cat_id === id)
            }
        },
        async getCategory() {
            const cat = await axios.get('/api/categories');
            this.categories.push(...cat.data)
        },
        async saveTime(event,id) {
            await axios.post('/api/record-time', {
                id : id,
                time : event.path[0].innerText
            })
        }
    },
    components: {ShowTask}
}
</script>

<style scoped>

</style>
