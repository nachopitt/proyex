
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Project, Status } from '@/types';
import { store as storeUpdate } from '@/routes/projects/updates';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { watch } from 'vue';

interface Props {
  project: Project;
  statuses: Status[];
}

const props = defineProps<Props>();

const form = useForm({
  description: '',
  project: {
    current_status: props.project.current_status,
    current_progress_percentage: props.project.current_progress_percentage,
  }
});

watch(() => props.project, (newProject) => {
  form.defaults({
    project: {
      current_status: newProject.current_status,
      current_progress_percentage: newProject.current_progress_percentage,
    }
  });
  form.reset();
}, {
  deep: true
});

const submit = () => {
  form.post(storeUpdate(props.project.id).url);
};
</script>

<template>
  <div class="max-w-xl space-y-12">
    <div class="flex flex-col space-y-6">
      <form @submit.prevent="submit" class="space-y-6">
        <div class="mt-4">
          <Label for="description">Description</Label>
          <Textarea id="description" v-model="form.description" class="mt-1 block w-full" required />
        </div>

        <div>
          <Label for="project.current_status">Current status</Label>
          <Select v-model="form.project.current_status">
              <SelectTrigger class="mt-1">
                  <SelectValue placeholder="Select a status" />
              </SelectTrigger>
              <SelectContent>
                  <SelectGroup>
                      <SelectLabel>Statuses</SelectLabel>
                      <SelectItem
                          v-for="status in statuses"
                          :key="status.id"
                          :value="String(status.id)"
                      >
                          {{ status.name }}
                      </SelectItem>
                  </SelectGroup>
              </SelectContent>
          </Select>
          <InputError class="mt-2" :message="form.errors['project.current_status']" />
        </div>

        <div>
          <Label for="project.current_progress_percentage">Current progress</Label>
          <Input
              id="project.current_progress_percentage"
              type="text"
              class="mt-1 block w-full"
              v-model="form.project.current_progress_percentage"
              required
              autofocus
          />
          <InputError class="mt-2" :message="form.errors['project.current_progress_percentage']" />
        </div>

        <div class="flex items-center justify-end mt-4">
          <Button :disabled="form.processing">
            Log Progress
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>
