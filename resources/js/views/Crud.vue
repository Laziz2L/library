<template>
    <div class="container mt-5">
        <button class="btn btn-success" @click="add">Add</button>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Year</th>
                    <th scope="col">Authors</th>
                    <th scope="col">Options</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(book, i) in books" :key="i">
                    <th scope="row">{{ book.id }}</th>
                    <td>{{ book.name }}</td>
                    <td>{{ book.year }}</td>
                    <td>
                        <p
                            v-for="(author, j) in book.authors"
                            :key="i + '-' + j"
                        >
                            {{ author.name }}
                        </p>
                    </td>
                    <td>
                        <button class="btn btn-primary" @click="edit(book.id)">
                            Edit
                        </button>
                        <button
                            class="btn btn-danger"
                            @click="deleteBook(book.id)"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-if="showModal">
            <transition name="modal">
                <div class="modal-mask">
                    <snackbar
                        ref="snackbar"
                        baseSize="100px"
                        :holdTime="5000"
                        :multiple="true"
                        :position="'bottom-center'"
                    />
                    <div class="modal-wrapper">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add book</h5>
                                    <button
                                        type="button"
                                        class="close"
                                        data-dismiss="modal"
                                        aria-label="Close"
                                        @click="hide"
                                    >
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form @submit.prevent="sendForm">
                                        <input
                                            v-model="modal.name"
                                            class="form-control mb-2"
                                            type="text"
                                            placeholder="name"
                                            required
                                        />
                                        <input
                                            v-model="modal.year"
                                            class="form-control mb-2"
                                            type="text"
                                            placeholder="year"
                                            required
                                            @input="formatYear"
                                        />
                                        <multiselect
                                            v-model="modal.authors"
                                            :options="authors"
                                            :multiple="true"
                                            :close-on-select="false"
                                            :clear-on-select="false"
                                            :preserve-search="true"
                                            placeholder="Pick author(s)"
                                            label="name"
                                            track-by="id"
                                            :preselect-first="false"
                                        />
                                        <button
                                            type="submit"
                                            class="btn btn-success mt-2"
                                        >
                                            Submit
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script>
import Snackbar from "vuejs-snackbar";
import Multiselect from "vue-multiselect";
export default {
    data() {
        return {
            books: [],
            authors: [],
            showModal: false,
            modal: {
                name: "",
                year: "",
                authors: [],
            },
            editId: false,
        };
    },
    methods: {
        show() {
            this.showModal = true;
        },
        hide() {
            this.showModal = false;
            this.modal = {
                name: "",
                year: "",
                authors: [],
            };
        },
        add() {
            this.show();
        },
        findBookById(id) {
            for (let i = 0; i < this.books.length; i++) {
                if (this.books[i].id == id) return this.books[i];
            }
            return false;
        },
        deleteBook(id) {
            axios
                .delete("/api/books/" + id)
                .then((res) => {
                    if (res.data.status) {
                        console.log(res.data);
                        this.loadBooks();
                    } else {
                        console.log(res.data);
                        this.$refs.snackbar.error(res.data.msg);
                    }
                })
                .catch((err) => console.error(err));
        },
        edit(id) {
            let book = this.findBookById(id);
            if (book) {
                this.modal.name = book.name;
                this.modal.year = book.year;
                this.modal.authors = book.authors;
                this.editId = id;
                this.showModal = true;
            } else console.log("err");
        },
        formatYear() {
            this.modal.year = this.modal.year.replace(/[^0-9]/g, "");
            this.modal.year = this.modal.year.slice(0, 4);
        },
        sendForm() {
            let body = Object.assign({}, this.modal);
            body.authors = body.authors.map((author) => {
                return author.id;
            });

            if (this.editId) {
                axios
                    .put("/api/books/" + this.editId, body)
                    .then((res) => {
                        this.editId = false;
                        if (res.data.status) {
                            console.log(res.data);
                            this.hide();
                            this.loadBooks();
                        } else {
                            console.log(res.data);
                            this.$refs.snackbar.error(res.data.msg);
                        }
                    })
                    .catch((err) => {
                        this.editId = false;
                        console.error(err);
                    });
            } else {
                axios
                    .post("/api/books", body)
                    .then((res) => {
                        if (res.data.status) {
                            console.log(res.data);
                            this.hide();
                            this.loadBooks();
                        } else {
                            console.log(res.data);
                            this.$refs.snackbar.error(res.data.msg);
                        }
                    })
                    .catch((err) => console.error(err));
            }
        },
        loadBooks() {
            axios
                .get("/api/books")
                .then((res) => {
                    if (res.data.status) {
                        this.books = res.data.books;
                        console.log(this.books);
                    } else {
                        console.log(res.data.msg);
                    }
                })
                .catch((err) => console.error(err));
        },
        loadAuthors() {
            axios
                .get("/api/authors")
                .then((res) => {
                    if (res.data.status) {
                        this.authors = res.data.authors;
                    } else {
                        console.log(res.data.msg);
                    }
                })
                .catch((err) => console.error(err));
        },
    },
    mounted() {
        this.loadBooks();
        this.loadAuthors();
    },
    components: {
        Multiselect,
        Snackbar,
    },
};
</script>

<style>
</style>
