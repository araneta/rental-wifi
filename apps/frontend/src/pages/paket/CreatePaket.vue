<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { apiFetch } from '../../api'
import { useToast } from 'vue-toastification'

export default {
  name: 'CreatePelanggan',
  setup() {
    const router = useRouter()
    const toast = useToast()

    const form = ref({
      nama: '',
      alamat: '',
      noHp: '',
      paketId: '',
      status: 'aktif',
      pop: ''
    })

    const pakets = ref([])

    onMounted(async () => {
      try {
        const response = await apiFetch('/pakets', { method: 'GET' })
        pakets.value = response.pakets || []
      } catch (e) {
        console.error('Gagal memuat paket', e)
        toast.error('Gagal memuat data paket.')
      }
    })

    const submitForm = async () => {
      try {
        await apiFetch('/pelanggans', {
          method: 'POST',
          body: JSON.stringify(form.value),
          headers: {
            'Content-Type': 'application/json'
          }
        })

        toast.success('Data berhasil disimpan!')
        router.push('/pakets')
      } catch (e) {
        console.error('Gagal menyimpan data', e)
        toast.error('Gagal menyimpan data.')
      }
    }

    return {
      form,
      pakets,
      submitForm
    }
  }
}
</script>
