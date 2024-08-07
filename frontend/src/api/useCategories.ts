import {notification} from "antd";
import {useQuery} from "@tanstack/react-query";

const useCategories = () => {
    return useQuery<string[]>({
        queryKey: ['project-categories'],
        queryFn: async () => {
            const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/projects/categories`);
            if (!response.ok) {
                notification.error({
                    message: 'Failed to fetch categories',
                    description: 'We will retry in 5 seconds',
                });
                throw new Error('Failed to fetch categories');
            }
            return await response.json();
        },
        retryDelay: 5000,
    })
};

export default useCategories;