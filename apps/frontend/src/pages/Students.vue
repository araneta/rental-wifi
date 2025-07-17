<script>
export default {
  data() {
    return {
      students: [],
      form: { id: null, name: '', email: '' }
    };
  },
  mounted() {
    this.fetchStudents();
  },
  methods: {
    fetchStudents() {
      fetch('/api/students', {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('token')
        }
      })
      .then(res => res.json())
      .then(data => this.students = data);
    },
    saveStudent() {
      const method = this.form.id ? 'PUT' : 'POST';
      const url = this.form.id ? `/api/students/${this.form.id}` : '/api/students';

      fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          Authorization: 'Bearer ' + localStorage.getItem('token')
        },
        body: JSON.stringify(this.form)
      }).then(() => {
        this.fetchStudents();
        this.form = { id: null, name: '', email: '' };
      });
    },
    editStudent(s) {
      this.form = { ...s };
    },
    deleteStudent(id) {
      fetch(`/api/students/${id}`, {
        method: 'DELETE',
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('token')
        }
      }).then(() => this.fetchStudents());
    }
  }
};
</script>

<template>
  <div>
    <h2>Students</h2>
    <input v-model="form.name" placeholder="Name" />
    <input v-model="form.email" placeholder="Email" />
    <button @click="saveStudent">Save</button>

    <ul>
      <li v-for="s in students" :key="s.id">
        {{ s.name }} - {{ s.email }}
        <button @click="editStudent(s)">Edit</button>
        <button @click="deleteStudent(s.id)">Delete</button>
      </li>
    </ul>
  </div>
</template>
