<template>
    <div class="container mt-3">
        <app-alert v-if="alert" :alert="alert" @close="alert = null"></app-alert>
        <h4 class="text-center"> Сортировка по категориям</h4>
        <div class="row">
            <div class="header">
                <select class="form-control category w-75 m-3"
                        v-model="category"
                        @change="getCards(category)"
                        name="category"
                        id="category">
                    <option :value="0">Выберите категорию</option>
                    <option v-for="cat in categories" :value="cat.id">{{cat.name}}</option>
                </select>
                <button class="btn btn-primary" @click="updateAllCards">Обновить все карты</button>
            </div>
            <div class="col-12 m-auto">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th style="width: 20%">Название</th>
                            <th>Среднее время</th>
                            <th>Ручной ввод</th>
                            <th style="width: 20%">Обновлено</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        <show-task v-for="(card,i) in cards" :card="card" :i="i" @update="updateCard"></show-task>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import ShowTask from "../components/ShowTask";
import AppAlert from "../../components/AppAlert";
export default {
    data() {
        return {
            data: null,
            cards: [],
            alert: null,
            category: 0,
            categories: []
        }
    },
    mounted(){
        this.getAllCards()
        this.getCategory()
    },
    methods: {
        updateCard(name) {
            window.scroll(0, 0)
            this.alert = {
                type: 'primary',
                title: 'Успешно!',
                text: `Тех карта ${name} обновлена!`
            }
            // setTimeout(() => window.location.reload(), 1000)
        },
        async  getAllCards() {
          const cards = await axios.get('/api/tech_card')
          this.cards.push(...cards.data)
        },
        async getCards(id) {
            if (id === 0) {
                const cards = await axios.get('/api/tech_card')
                this.cards.push(...cards.data)
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
        updateAllCards() {
          let card = confirm("Вы действительно хотите обновить все карты?");

          alert(card ? 'Вы запустили процесс обновления' : 'привильно и так сойдут!')
        }
    },
    components: {ShowTask, AppAlert}
}
</script>

<style scoped>
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>
