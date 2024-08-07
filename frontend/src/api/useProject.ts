import {notification} from "antd";
import {useQuery} from "@tanstack/react-query";

const useProject = (id: number) => {
    return useQuery({
        queryKey: ['project', id],
        queryFn: async () => {
            const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/projects/${id}`);

            if (!response.ok) {
                const res = await response.json();
                notification.error({
                    message: 'Failed to fetch project',
                    description: 'We will retry in 5 seconds',
                });
                throw new Error(res.error);
            }

            return await response.json();
        },
        retryDelay: 5000,
    })
};

export default useProject;