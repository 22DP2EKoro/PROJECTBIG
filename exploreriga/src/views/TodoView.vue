<template>
  
  <div id="app" class="container py-5">
    <div class="card shadow-sm rounded-4 p-4">
      <h2 class="text-center mb-4">📝 ExploreRiga Todo</h2>

      <!-- Input Field -->
      <div class="input-group mb-4">
        <input
          v-model="task"
          type="text"
          class="form-control rounded-start"
          placeholder="What do you need to do?"
        />
        <button @click="submitTask" class="btn btn-warning rounded-end px-4">
          {{ editedTask === null ? 'Add' : 'Update' }}
        </button>
      </div>

      <!-- Task Table -->
      <div v-if="tasks.length > 0" class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>TASK</th>
              <th>STATUS</th>
              <th class="text-center">EDIT</th>
              <th class="text-center">DELETE</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(task, index) in tasks" :key="index">
              <td>
                <span :class="{ finished: task.status === 'finished' }">{{ task.name }}</span>
              </td>
              <td>
                <span
                  class="badge rounded-pill px-3 py-2"
                  :class="{
                    'bg-danger': task.status === 'to-do',
                    'bg-warning text-dark': task.status === 'in progress',
                    'bg-success': task.status === 'finished'
                  }"
                  role="button"
                  @click="changeStatus(index)"
                >
                  {{ firstCharUpper(task.status) }}
                </span>
              </td>
              <td class="text-center">
                <i
                  class="fa fa-pen text-primary pointer"
                  title="Edit"
                  @click="editTask(index)"
                ></i>
              </td>
              <td class="text-center">
                <i
                  class="fa fa-trash text-danger pointer"
                  title="Delete"
                  @click="deleteTask(index)"
                ></i>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="text-center text-muted mt-4">
        <em>No tasks yet. Start adding some!</em>
      </div>
    </div>
  </div>

</template>


<script>
export default {
  name: 'App',
  data() {
    return {
      task: '', // bind to input field
      editedTask: null, // Track if the task is being edited
      availableStatuses: ['to-do', 'in progress', 'finished'],
      tasks: [
        { name: 'Steal bananas from the store.', status: 'to-do' },
        { name: 'Eat 1kg chocolate in 1 hour.', status: 'in progress' }
      ]
    };
  },
  methods: {
    submitTask() {
      if (this.task.trim() === '') return; // Don't allow empty tasks

      if (this.editedTask === null) {
        // Add new task
        this.tasks.push({ name: this.task, status: 'to-do' });
      } else {
        // Edit task
        this.tasks[this.editedTask].name = this.task;
        this.editedTask = null;
      }

      this.task = ''; // Reset input field
    },
    deleteTask(index) {
      // Delete task by index
      this.tasks.splice(index, 1);
    },
    editTask(index) {
      // Set task to be edited
      this.task = this.tasks[index].name;
      this.editedTask = index;
    },
    changeStatus(index) {
      // Cycle through task statuses
      let newIndex = this.availableStatuses.indexOf(this.tasks[index].status);
      this.tasks[index].status = this.availableStatuses[(newIndex + 1) % this.availableStatuses.length];
    },
    firstCharUpper(str) {
      // Helper method to capitalize first character of status
      return str.charAt(0).toUpperCase() + str.slice(1);
    }
  }
};
</script>

<style scoped>

.pointer {
  cursor: pointer;
}
.finished {
  text-decoration: line-through;
}
</style>
