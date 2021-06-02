<template>
    <nav id="sidebar" class="sidebar js-sidebar">
        <div class="sidebar-content js-simplebar">
            <router-link :to="{ path: `/manager/`}" active-class="active" class="sidebar-brand">
                <span class="align-middle">HiTE-PRO</span>
            </router-link>

            <ul class="sidebar-nav">
                <li class="sidebar-header">
                    Pages
                </li>

                <li class="sidebar-item active">
                    <router-link :to="{ path: `/manager/`}" class="sidebar-link">
                        <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Панель</span>
                    </router-link>
                </li>

                <li class="sidebar-item">
                    <router-link :to="{ path: `/manager/`}" class="sidebar-link">
                        <i class="align-middle" data-feather="user"></i> <span class="align-middle">Профиль</span>
                    </router-link>
                </li>

                <li class="sidebar-item">
                    <router-link :to="{ path: `/manager/users/`}" class="sidebar-link">
                        <i class="align-middle" data-feather="users"></i> <span class="align-middle">Сотрудники</span>
                    </router-link >
                </li>

                <li class="sidebar-item">
                    <router-link :to="{ path: `/manager/listmap`}" class="sidebar-link">
                        <i class="align-middle" data-feather="list"></i> <span class="align-middle">Тех. карты</span>
                    </router-link>
                </li>

                <li class="sidebar-header">
                    Records
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="/charts-chartjs">
                        <i class="align-middle" data-feather="bar-chart-2"></i> <span class="align-middle">Charts</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
export default {

}
</script>

<style scoped>

</style>
