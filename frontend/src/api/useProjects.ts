import {useQuery} from "@tanstack/react-query";
import {Project} from "@/types/Project";
import {useMemo, useState} from "react";
import {notification} from "antd";

export type ProjectsResponse = Array<Project>;
export type UseProjectsArgs = {
    initialFilters: Record<string, any>
    owner?: number
}

export function useProjects({ initialFilters, owner }: UseProjectsArgs) {
    const [filters, setFilter] = useState<Record<string, any>>(initialFilters);

    const query = useQuery<ProjectsResponse>({
        queryKey: ['projects', { ...filters }, owner ],
        queryFn: async () => {
            const query = new URLSearchParams();

            for (const key in filters) {
                query.set(`filters[${key}]`, filters[key]);
            }

            if (owner) {
                query.set('filters[owner]', owner.toString());
            }

            const url = new URL(`${process.env.NEXT_PUBLIC_API_URL}/projects`);
            url.search = query.toString();

            const response = await fetch(url);

            if (!response.ok) {
                const res = await response.json();
                notification.error({
                    message: 'Failed to fetch projects',
                    description: res.error,
                });
                throw new Error(res.error);
            }

            if (response.status === 204) {
                return [];
            }

            return await response.json();
        },
        refetchOnWindowFocus: false
    });
    
    const isBusy = useMemo(() => 
        query.isFetching || 
        query.isRefetching ||
        query.isPending
        , [query.isFetching, query.isPending, query.isRefetching]);

    return { query, isBusy, filters, setFilter };
}