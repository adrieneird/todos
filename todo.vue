<template>
    <div v-for="todo in todos">
        {{ todo.tache }} - {{ todo.echeance }}
        
        <button @click="etatTodo(todo)">{{ afficheEtat(todo.etat) }}</button>
        <button @click="editTodo(todo)">Editer</button>
        <button @click="delTodo(todo)">Supprimer</button>
    </div>
    
    <h2>Ajouter un Todo</h2>
    <form>
        <input v-model="todoTexte" />
        <input type="date" v-model="todoEcheance" />
        <input type="hidden" v-model="todoId" />
        <button type="button" @click="saveTodo()">{{ afficheEditMode() }}</button>
        <button type="button" @click="cancelEdit()" v-show="(afficheEditMode()!='Ajouter')">Annuler</button>
    </form>
</template>

<script>
import { ref } from 'vue'

export default {
    name: 'Todo',
    setup() {
        
        const todos = ref(null);
        const todoTexte = ref(null);
        const todoEcheance = ref(null);
        const todoId = ref(null);
        const editMode = ref(0);
        
        async function getTodos() {
            // Appel à l'API pour récup les todos
            const response = await fetch("https://adrienboeglin.sites.3wa.io/todo/index.php?action=read");
            todos.value = await response.json();
        }
        getTodos();
        
        function afficheEtat(etat) {
            if (etat===1) {
                return "Fait";
            }
            return "A faire";
        }
        
        function afficheEditMode() {
            if (editMode.value === 0) {
                return "Ajouter";
            } else {
                return "Mettre à jour";
            }
        }
        
        function editTodo(todo) {
            todoTexte.value = todo.tache;
            todoEcheance.value = todo.echeance;
            todoId.value = todo.id;
            editMode.value = 1;
        }
        
        function delTodo(todo) {
            fetch(
                "https://adrienboeglin.sites.3wa.io/todo/index.php",
                {
                    method: "POST",
                    headers: { "Content-type": "application/x-www-form-urlencoded; charset=UTF-8" },
                    body: "action=delete&id="+todo.id,
                }
            )
            .then(
                () => {
                    getTodos();
                }
            );
        }
        
        function saveTodo() {
            if (editMode.value === 0) {
                // Ajout
                
                fetch(
                "https://adrienboeglin.sites.3wa.io/todo/index.php",
                    {
                        method: "POST",
                        headers: { "Content-type": "application/x-www-form-urlencoded; charset=UTF-8" },
                        body: "action=create&tache="+todoTexte.value+"&echeance="+todoEcheance.value,
                    }
                )
                .then(
                    () => {
                        getTodos();
                    }
                );
            } else {
                // Edit
                
                fetch(
                "https://adrienboeglin.sites.3wa.io/todo/index.php",
                    {
                        method: "POST",
                        headers: { "Content-type": "application/x-www-form-urlencoded; charset=UTF-8" },
                        body: "action=update&id="+todoId.value+"&tache="+todoTexte.value+"&echeance="+todoEcheance.value,
                    }
                )
                .then(
                    () => {
                        cancelEdit();
                        getTodos();
                    }
                );
            }
        }
        
        function etatTodo(todo) {
            let etat = 0;
            if (todo.etat === 0) {
                etat = 1;
            }
            
            fetch(
            "https://adrienboeglin.sites.3wa.io/todo/index.php",
                {
                    method: "POST",
                    headers: { "Content-type": "application/x-www-form-urlencoded; charset=UTF-8" },
                    body: "action=update&id="+todo.id+"&etat="+etat,
                }
            )
            .then(
                () => {
                    getTodos();
                }
            );
        }
        
        function cancelEdit() {
            todoTexte.value = null;
            todoEcheance.value = null;
            todoId.value = null;
            editMode.value = 0;
        }
        
        // Exposition des variables
        return {
            todos,
            todoTexte,
            todoEcheance,
            todoId,
            afficheEtat,
            editTodo,
            delTodo,
            afficheEditMode,
            saveTodo,
            cancelEdit,
            etatTodo,
        };
    }
};
</script>