"use client";
import {useMutation, useQueryClient} from "@tanstack/react-query";
import {useUser} from "@/helper/user";
import {notification} from "antd";
import {useRouter} from "next/navigation";

export type UseNewProjectProps = {
    successCallback?: () => void;
    failureCallback?: () => void;
}

const useDeleteProject = (props?: UseNewProjectProps) => {
    const { token } = useUser();
    const queryClient = useQueryClient();

    const {
        data,
        status,
        mutate
    } = useMutation({
        mutationFn: async (id: string) => {
            const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/projects/${id}`, {
                method: 'DELETE',
                mode: 'cors',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(data),
            });

            if (!response.ok) {
                const res = await response.json();
                throw new Error(res.error);
            }

            return {};
        },
        onSuccess: () => {
            if (props?.successCallback) {
                props?.successCallback();
            }

            queryClient.invalidateQueries({
                queryKey: ['projects'],
                stale: true,
                type: 'all'
            }).then()

            notification.success({
                message: 'Project deleted',
                description: 'The project has been deleted'
            });
        },
        onError: (error) => {
            if (props?.failureCallback) {
                props?.failureCallback();
            }

            notification.error({
                message: 'Failed to delete project',
                description: error.message || 'An error occurred'
            });
        }
    })

    return {
        data,
        status,
        mutate
    }
};

export default useDeleteProject;