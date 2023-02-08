<template>
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 280px;">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
            <b-icon icon="bootstrap-fill"></b-icon>
            <span class="fs-4">Admin</span>
        </a>

        <hr />

        <ul class="nav nav-pills flex-column mb-auto">
            <li>
                <router-link :to="{name: 'admin'}" class="nav-link link-dark" exact>
                    <b-icon icon="graph-up"></b-icon>
                    Dashboard
                </router-link>
            </li>
            <li>
                <router-link :to="{name: 'admin.sorteios'}" class="nav-link link-dark" exact>
                    <b-icon icon="star"></b-icon>
                    Sorteios
                </router-link>
            </li>
            <li>
                <a href="#" class="nav-link link-dark">
                    <b-icon icon="bank"></b-icon>
                    Jogos
                </a>
            </li>
            <li>
                <a href="#" class="nav-link link-dark">
                    <b-icon icon="person-circle"></b-icon>
                    Usuários
                </a>
            </li>
        </ul>

        <hr />

        <div class="dropdown">
            <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
                <strong>mdo</strong>
            </a>
            <ul class="dropdown-menu text-small shadow">
                <li><a class="dropdown-item" href="#">New project...</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#">Sign out</a></li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    components: {
        
    },

    data() {
        return {
            
        }
    },

    created: function() {

    },

    methods: {
        
    }
}
</script>

<style scoped>
.nav-link:hover {
    background-color: #cdcdcd;
}

.b-icon {
    margin-right: 20px;
}
</style>