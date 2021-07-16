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
            <div class="col-8">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Название</th>
                            <th>Среднее время</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(card,i) in cards">
                            <td>{{i+1}}</td>
                            <td>{{card.name}}</td>
                            <td contenteditable @blur="saveTime($event, card.id)" >
                                {{ (card.statistical_time ? card.statistical_time : (card.dynamic_time)/60).toFixed(1)}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

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
        console.log(this.cards)
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
    }
}
</script>

<style scoped>

</style>
