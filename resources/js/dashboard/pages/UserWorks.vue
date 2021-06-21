<template>
    <div class="main">
        <h4 class="text-center">Статус работ</h4>
        <table class="table table-striped mt-3">
            <thead>
            <tr>
                <th style="width:22%;">Сотрудник</th>
                <th style="width:22%;">Тех. карта</th>
                <th style="width:22%">Количество</th>
                <th class="d-none d-md-table-cell" style="width:22%">Брак</th>
                <th class="d-none d-md-table-cell" style="width:22%">Статус</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Устинов</td>
                    <td>LV-1-case</td>
                    <td>100</td>
                    <td>2</td>
                    <td><button class="btn btn-primary">провести</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {

}
</script>

<style scoped>
.main {
    margin: 20px 15px;
}
</style>
