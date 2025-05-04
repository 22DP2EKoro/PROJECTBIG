<template>
  <div class="container mt-5">
    <h2 class="text-center">ExploreRiga Todo</h2>

    <!-- Input Field -->
    <div class="d-flex">
      <input v-model="task" type="text" placeholder="Enter task" class="form-control" />
      <button @click="submitTask" class="btn btn-warning rounded-0 ms-2">SUBMIT</button>
    </div>

    <!-- Task Table -->
    <table class="table table-bordered mt-5">
      <thead>
        <tr>
          <th scope="col">TASK</th>
          <th scope="col">STATUS</th>
          <th scope="col" class="text-center">EDIT</th>
          <th scope="col" class="text-center">DELETE</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(task, index) in tasks" :key="index">
          <td>
            <span :class="{ finished: task.status === 'finished' }">{{ task.name }}</span>
          </td>
          <td style="width: 120px">
            <span
              @click="changeStatus(index)"
              class="pointer"
              :class="{
                'text-danger': task.status === 'to-do',
                'text-warning': task.status === 'in progress',
                'text-success': task.status === 'finished'
              }"
            >
              {{ firstCharUpper(task.status) }}
            </span>
          </td>
          <td class="text-center">
            <span class="fa fa-pen pointer" @click="editTask(index)"></span>
          </td>
          <td class="text-center">
            <span class="fa fa-trash pointer text-danger" @click="deleteTask(index)"></span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: "ExploreRigaTodo",
  data() {
    return {
      task: "",
      editedTask: null,
      availableStatuses: ["to-do", "in progress", "finished"],
      tasks: [
        { name: "Steal bananas from the store.", status: "to-do" },
        { name: "Eat 1kg chocolate in 1 hour.", status: "in progress" },
      ],
    };
  },
  methods: {
    submitTask() {
      if (this.task.trim() === "") return;

      if (this.editedTask === null) {
        this.tasks.push({ name: this.task, status: "to-do" });
      } else {
        this.tasks[this.editedTask].name = this.task;
        this.editedTask = null;
      }

      this.task = "";
    },
    deleteTask(index) {
      this.tasks.splice(index, 1);
    },
    editTask(index) {
      this.task = this.tasks[index].name;
      this.editedTask = index;
    },
    changeStatus(index) {
      let newIndex = this.availableStatuses.indexOf(this.tasks[index].status);
      this.tasks[index].status =
        this.availableStatuses[(newIndex + 1) % this.availableStatuses.length];
    },
    firstCharUpper(str) {
      return str.charAt(0).toUpperCase() + str.slice(1);
    },
  },
};
</script>

<style scoped>
body {
  background-color: #f8f9fa;
}
.pointer {
  cursor: pointer;
}
.finished {
  text-decoration: line-through;
}
</style>
