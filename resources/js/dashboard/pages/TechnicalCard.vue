<template>
    <div class="container mt-3">
        <app-loader v-if="loading"></app-loader>
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
import AppLoader from '../../components/AppLoader'
export default {
    data() {
        return {
            data: null,
            cards: [],
            alert: null,
            category: 0,
            loading: false,
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
        async updateCard(data) {
            this.loading = true
            const response = await axios.post('/api/update-card/' + data.id)
            if (response.status === 200) {
                window.scroll(0, 0)
                this.alert = {
                    type: 'primary',
                    title: 'Успешно!',
                    text: `Тех карта ${data.name} обновлена!`
                }
            }
            this.loading = false

            setTimeout(() => window.location.reload(), 1000)
        },
        async updateAllCards() {
          let card = confirm("Вы действительно хотите обновить все карты?");
          if(card) {
              this.loading = true
              const data = await axios.get('/api/update-all-cards')
              this.loading = false
              window.scroll(0, 0)
              if (data.status === 200) {
                  this.alert = {
                      type: 'primary',
                      title: 'Успешно!',
                      text: `Все тех карты обновлены!`
                  }
              } else {
                  this.alert = {
                      type: 'danger',
                      title: 'Ошибка!',
                      text: `Проблема на стороне сервера!`
                  }
              }

          }
        }
    },
    components: {ShowTask, AppAlert, AppLoader}
}
</script>

<style scoped>

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>
