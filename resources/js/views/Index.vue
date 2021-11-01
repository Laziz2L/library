<template>
    <div>
        <div class="card text-center">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a
                            class="nav-link"
                            :class="{ active: tab == 'books' }"
                            @click.prevent="tab = 'books'"
                            aria-current="true"
                            href="#"
                            >Books</a
                        >
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link"
                            :class="{ active: tab == 'authors' }"
                            @click.prevent="tab = 'authors'"
                            href="#"
                            >Authors</a
                        >
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link"
                            :class="{ active: tab == 'authorsWithBooksCount' }"
                            @click.prevent="tab = 'authorsWithBooksCount'"
                            href="#"
                            >Authors with books count</a
                        >
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <crud v-show="tab == 'books'"></crud>
                <authors :mode="'basic'" v-show="tab == 'authors'"></authors>
                <authors :mode="'withBooksCount'" v-show="tab == 'authorsWithBooksCount'"></authors>
            </div>
        </div>
    </div>
</template>

<script>
import Crud from "./Crud.vue";
import Authors from "./Authors.vue";
export default {
    data() {
        return {
            tab: "books",
        };
    },

    components: {
        Crud,
        Authors
    },
};
</script>

<style>
</style>
