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

                <li class="sidebar-item" >
                    <router-link :to="{ path: `/manager/`}" :active="isActive" class="sidebar-link">
                        <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Панель</span>
                    </router-link>
                </li>

                <li class="sidebar-item">
                    <router-link :to="{ path: `/manager/profile`}" class="sidebar-link">
                        <i class="align-middle" data-feather="user"></i> <span class="align-middle">Профиль</span>
                    </router-link>
                </li>

                <li class="sidebar-item">
                    <router-link :to="{ path: `/manager/users/`}" class="sidebar-link">
                        <i class="align-middle" data-feather="users"></i> <span class="align-middle">Сотрудники</span>
                    </router-link >
                </li>

                <li class="sidebar-item">
                    <router-link :to="{ path: `/manager/manufacture/`}" class="sidebar-link">
                        <i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Производство</span>
                    </router-link >
                </li>

                <li class="sidebar-item">
                    <router-link :to="{ path: `/manager/list-map`}" class="sidebar-link">
                        <i class="align-middle" data-feather="list"></i> <span class="align-middle">Тех. карты</span>
                    </router-link>
                </li>

                <li class="sidebar-item">
                    <router-link :to="{ path: `/manager/journal`}" class="sidebar-link">
                        <i class="align-middle" data-feather="clipboard"></i> <span class="align-middle">Журнал</span>
                    </router-link>
                </li>
                <li class="sidebar-item">
                    <router-link :to="{ path: `/manager/logs`}" class="sidebar-link">
                        <i class="align-middle" data-feather="clipboard"></i> <span class="align-middle">Журнал ошибок</span>
                    </router-link>
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
