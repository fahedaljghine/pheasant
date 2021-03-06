<template>
<div v-if="activeColumn === 'integrations'" class="bg-gray-100 rounded-b">
  <div class="flex flex-row md:p-8">
    <div v-for="(service, index) in services" class="p-4 bg-white shadow rounded flex flex-col" :class="[index !== 0 ? 'border-t' : '']">
      <div class="flex justify-between items-center">
        <div class="text-lg md:text-xl font-medium text-gray-700 uppercase">
          {{ service.name }}
        </div>
        <div class="flex items-center">
          <div class="text-sm pr-2" :class="[service.enabled ? 'text-green-500' : 'text-gray-700']">
            {{ service.enabled ? 'Enabled' : 'Disabled' }}
          </div>
          <div @click="toggleServiceStatus(service)" :class="[service.enabled ? 'bg-indigo-400 justify-end' : 'bg-gray-500 justify-start']" class="w-10 h-4 rounded-full flex flex-row items-center p-1 cursor-pointer" :title="[service.enabled ? 'Disable' : 'Enable']">
            <div class="rounded-full w-3 h-3 bg-white"></div>
          </div>
        </div>
      </div>
      <div class="flex flex-row items-center pt-4">
        <a href="https://github.blog/2013-05-16-personal-api-tokens/" class="underline text-blue-500 text-sm pr-4">How to get a personal access token</a>
        <div class="">
          <button @click="showSetupModal" class="px-4 py-2 font-semibold bg-teal-500 text-white rounded">Set Token</button>
        </div>
      </div>
    </div>
  </div>

  <div class="">
    <div :class="{'hidden': !modalShown}" class="absolute container mx-auto w-5/6 md:w-3/5 lg:w-2/5 bg-white rounded shadow-lg z-10" style="top: 12vh;left: 0;right: 0;">
      <div class="p-4">
        <div class="p-4">
          <label class="block uppercase tracking-wide text-gray-800 text-xs font-bold mb-2" for="grid-first-name">
            Github Personal Access Token <span class="text-gray-500 capitalize">(required)</span>
          </label>
          <input ref="focusInput" v-model="access_token" class="appearance-none block w-full bg-gray-200 text-gray-800 border border-gray-200 rounded py-3 px-4" type="password" placeholder="*******" required>
        </div>
      </div>
      <div class="flex flex-row justify-between py-4 px-8 bg-gray-200 rounded">
        <button @click="closeSetupModal" class="text-red-lighter hover:font-bold hover:text-red-400">Cancel</button>
        <button @click="saveToken" class="bg-teal-400 text-white font-medium hover:bg-teal-600 py-2 px-4 rounded">Save</button>
      </div>
    </div>
    <div @click="closeSetupModal" :class="{'hidden': !modalShown}" class="h-screen w-screen fixed inset-0 bg-gray-900 opacity-25"></div>
  </div>
</div>  
</template>

<script>
import { mapActions } from 'vuex'

export default {
  props: {
    activeColumn: {
      required: true,
      type: String
    }
  },

  data: () => ({
    modalShown: false,
    access_token: '',
    services: [],
    update: false
  }),

  mounted () {
    axios.get('/admin/services')
      .then((response) => {
        this.services = response.data.services
      })
      .catch((error) => {
        console.error(error.response.data.message)
      })
  },

  methods: {
    ...mapActions([
      'showNotification',
    ]),
    showSetupModal () {
      this.modalShown = true
    },
    closeSetupModal () {
      this.modalShown = false
    },
    toggleServiceStatus (service) {
      if (service.active) {
        axios.put('/admin/services/' + service.name, {
          status: !service.enabled
        })
          .then((response) => {
            service.enabled = !service.enabled
            this.showNotification({type: response.data.status, message: response.data.message})
          })
          .catch((error) => {
            this.showNotification({type: error.response.data.status, message: error.response.data.message})
          })
      } else {
        this.showSetupModal()
      }
    },
    saveToken () {
      axios.post('admin/services', {
        name: 'github',
        access_token: this.access_token
      })
        .then((response) => {
          this.modalShown = false
          this.showNotification({type: response.data.status, message: response.data.message})
        })
        .catch((error) => {
          this.showNotification({type: error.response.data.status, message: error.response.data.message})
       })
    }
  }
}
</script>
