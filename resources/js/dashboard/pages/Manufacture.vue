<template>
    <h4 class="text-center mt-3">Производство</h4>
    <div class="table-scroll">
    <table class="table table-striped table-hover mt-3">
        <thead>
        <tr>
            <th>#</th>
            <th style="width: 25%">Название</th>
            <th>Коэффициент</th>
            <th>Остаток</th>
            <th>Резервы</th>
            <th>Продажи в день</th>
            <th>Необх-е кол-во</th>
            <th>Обновлено</th>
        </tr>
        </thead>
        <tbody>
            <tr v-for="(item,i) in data">
                <td>{{i+1}}</td>
                <td>{{item.name}}</td>
                <td contenteditable @blur="setRatio($event, item.id)">
                    {{item.salesRatio}}
                </td>
                <td>{{item.stock}}</td>
                <td>{{item.reserve}}</td>
                <td>{{saleToday(item)}}</td>
                <td>{{outputCount(item)}}</td>
                <td>{{item.updated_at.slice(0,10)}}</td>
            </tr>
        </tbody>
    </table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            data: []
        }
    },
    methods: {
        async getData() {
           const data = await axios.get('/api/sales-ratio')
           this.data.push(...data.data)
        },
        async setRatio(event, id) {
            // await axios.post('/api/sales-ratio/'+id, {ratio: event.path[0].innerText})
            console.log(event)
        },
        saleToday(item) {
            return Math.ceil((item.sellQuantity - item.returnQuantity)/30)
        },
        outputCount(item) {
            const count = ((item.sellQuantity - item.returnQuantity) * 90 * item.salesRatio) - item.stock + item.reserve
            if (count <= 0) {
                return 0
            }
            return count
        }
    },
    mounted() {
        this.getData()
    }
}
</script>

<style scoped>
.table-scroll {
    position: relative;
    width:100%;
    z-index: 1;
    margin: auto;
    overflow: auto;
    height: 100vh;
}

.table-scroll table {
    width: 98%;
    min-width: 1080px;
    margin: auto;
    border-collapse: separate;
    border-spacing: 0;
}

.table-scroll th,
.table-scroll td {
    background: #fff;
    text-align: center;
    vertical-align: middle;
}

.table-scroll thead th {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
}

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
