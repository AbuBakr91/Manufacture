<template>
    <div class="container mt-3">
        <div class="row">
            <div class="col-8">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Название</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(card,i) in cards[0]">
                            <td >{{i+1}}</td>
                            <td>{{card.name}}</td>
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
            cards: []
        }
    },
    mounted(){
        this.getCards()
        console.log(this.cards)
    },
    methods: {
      async  getCards() {
          const cards = await axios.get('/api/cards')
          this.cards.push(cards.data)
        }
    }
}
</script>

<style scoped>

</style>
