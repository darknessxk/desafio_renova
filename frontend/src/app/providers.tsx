'use client'

import {
    isServer,
    QueryClient,
    QueryClientProvider,
} from '@tanstack/react-query'

function makeQueryClient() {
    return new QueryClient({
        defaultOptions: {
            queries: {
                staleTime: 60 * 1000,
            },
        },
    })
}

let browserQueryClient: QueryClient | undefined = undefined

function getQueryClient() {
    if (isServer) {
        return makeQueryClient()
    } else {
        if (!browserQueryClient) browserQueryClient = makeQueryClient()
        return browserQueryClient
    }
}

// @ts-ignore
export default function Providers({ children }) {
    const queryClient = getQueryClient()

    return (
        <QueryClientProvider client={queryClient}>{children}</QueryClientProvider>
    )
}