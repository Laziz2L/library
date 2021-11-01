<template>
    <div class="container text-left">
        <table class="table mt-3">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Books{{mode == 'withBooksCount' ? ' count' : ''}}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(author, i) in authors" :key="i">
                    <th scope="row">{{ author.id }}</th>
                    <td>{{ author.name }}</td>
                    <td v-if="mode == 'basic'">
                        <p
                            v-for="(book, j) in author.books"
                            :key="i + '-' + j"
                        >
                            {{ book.name }}
                        </p>
                    </td>
                    <td v-if="mode == 'withBooksCount'">{{ author.books_count }}</td>
                </tr>
            </tbody>
        </table>


    </div>
</template>

<script>
export default {
    props: ['mode'],
    data() {
        return {
            authors: [],
            url: ''
        };
    },
    methods: {
        loadAuthors() {
            if (this.mode == 'basic') {
                this.url = "/api/authors"
            } else if (this.mode == 'withBooksCount') {
                this.url = "/api/authorsWithBooksCount"
            }
            axios
                .get(this.url)
                .then((res) => {
                    if (res.data.status) {
                        console.log(res.data);
                        this.authors = res.data.authors;
                    } else {
                        console.log(res.data);
                    }
                })
                .catch((err) => console.error(err));
        },
    },
    mounted() {
        this.loadAuthors();
    }
};
</script>

<style>
</style>
