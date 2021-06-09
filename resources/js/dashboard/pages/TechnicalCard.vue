<template>
    <div class="container mt-3">
        <div class="row">
            <div class="col-8">
                <h5 v-for="card in cards[0]">
                    {{card.name}}
                </h5>
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
